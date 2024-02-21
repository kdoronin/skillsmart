## Написание нескольких сотен строк по TDD:

Первая сложность возникла с тем, чтобы из текущих Python-проектов выделить максимально-независимый класс для того, чтобы реализовать его в соответствии с TDD. 

В итоге выбрал класс, который будет взаимодействовать с конкретной папкой в Google Drive и выполнять операции с файлами на ней.

Также в самой методике у меня существуют пробелы в понимании. Их уже начал восполнять, взявшись за рекомендуемую книгу по TDD. К сожалению, в 8-дневный срок полное её прочтение уложить не удалось.

Вторая сложность по коду была в том, что часть моего текущего проекта работает асинхронно. А уже на первых страницах книги по TDD было написано, что асинхронный код по TDD реализовывать не особо удобно. Подозреваю, что ближе к окончанию книги эту проблему разрешат, но я до туда ещё не добрался.

Третья сложность оказалась в том, что основной рабочий проект очень тяжело подогнать под TDD. Из-за слишком большого количества зависимостей внутри CMS. 

За счёт моего стремления к независимости целевого класса, в него оказались добавлены методы для работы с локальным хранилищем. Например, получение списка файлов в локальном хранилище. Впоследствии этот класс надо будет отделить от класса GoogleDrive.

В итоге получился такой, весьма перегруженный, класс с тестами:

```import unittest

from lliza.google_drive.google_drive import GoogleDrive


class GoogleDriveTestCase(unittest.TestCase):
    def setUp(self):
        self.folder_name = "Test folder"
        self.local_folder_name = 'test_files'
        self.file_name = '1.jpg'
        self.file_name_2 = '2.jpg'
        self.doc_name = 'text_en.docx'
        self.doc_name_2 = 'text_ru.docx'
        self.google_drive = GoogleDrive()
        self.parent_id = self.google_drive.get_parent_id()
        self.file_id = self.google_drive.upload_file(self.local_folder_name + self.file_name, self.folder_id)

    def test_create_folder(self):
        self.folder_id = self.google_drive.create_folder(self.folder_name, self.parent_id)
        self.assertTrue(isinstance(self.folder_id, str))

    def test_get_folder_name(self):
        folder_name = self.google_drive.get_folder_name(self.folder_id)
        self.assertEqual(self.folder_name, folder_name)

    def test_upload_file_to_folder(self):
        file_name = self.google_drive.get_file_name(self.file_id)
        self.assertEqual(self.file_name, file_name)

    def test_rename_file(self):
        name_changed = self.google_drive.change_file_name(self.file_id, '2_' + self.file_name)
        self.assertTrue(name_changed)
        file_name = self.google_drive.get_file_name(self.file_id)
        self.assertEqual(file_name, '2_' + self.file_name)

    def test_delete_file_from_folder(self):
        delete_result = self.google_drive.delete_file(self.file_id)
        self.assertTrue(delete_result)
        self.assertFalse(self.google_drive.is_file_exists(self.file_id))

    def test_upload_files_to_folder(self):
        self.files_names_list = self.google_drive.get_local_files_names(self.local_folder_name)
        is_uploaded = self.google_drive.upload_files(self.files_names_list, self.local_folder_name)
        self.assertTrue(is_uploaded)
        self.files_ids = self.google_drive.get_folder_files_ids(self.folder_id)
        self.assertTrue(isinstance(self.files_ids, list))

    def test_download_files_from_folder(self):
        is_downloaded = self.google_drive.download_files_from_folder(self.folder_id, self.local_folder_name + '_2')
        self.assertTrue(is_downloaded)
        files_new_names_list = self.google_drive.get_local_files_names(self.local_folder_name + '_2')
        self.assertEqual(self.files_names_list, files_new_names_list)

    def test_download_folder(self):
        is_downloaded = self.google_drive.download_folder(self.folder_id, self.local_folder_name + '_3')
        self.assertTrue(is_downloaded)
        files_new_names_list = self.google_drive.get_local_files_names(self.local_folder_name + '_3')
        self.assertTrue(self.folder_name + '.zip' in files_new_names_list)

    def test_get_images_ids_from_folder(self):
        file_list = [self.file_name, self.file_name_2, self.doc_name, self.doc_name_2]
        is_uploaded = self.google_drive.upload_files(file_list, self.local_folder_name + '_4')
        self.assertTrue(is_uploaded)
        images_ids_list = self.google_drive.get_images_ids(self.local_folder_name + '_4')
        images_list = [self.file_name, self.file_name_2]
        for image_id in images_ids_list:
            file_name = self.google_drive.get_file_name(image_id)
            self.assertTrue(file_name in images_list)

    def test_get_docs_from_folder(self):
        docs_ids_list = self.google_drive.get_docs_ids(self.local_folder_name + '_4')
        docs_list = [self.doc_name, self.doc_name_2]
        for doc_id in docs_ids_list:
            file_name = self.google_drive.get_file_name(doc_id)
            self.assertTrue(file_name in docs_list)

    def test_delete_folder(self):
        is_deleted = self.google_drive.delete_folder(self.folder_id)
        self.assertTrue(is_deleted)
        folder_exists = self.google_drive.is_folder_exists(self.folder_id)
        self.assertFalse(folder_exists)

    def tearDown(self) -> None:
        pass
```

Сам по себе метод TDD подсвечивает сущности, которые должны быть разделены ещё на этапе проектирования. И тестироваться отдельно. Приступил к тому, чтобы это разделение осуществить

И вот уже на этом этапе пришло осознание, что дальше писать код без постоянного обдумывания его архитектуры и дизайна смысла не имеет. Возможно, причина в том, что изначально приступил к работе с чистым посылом TDD, без дополнительного обдумывания, какие именно абстракции в моём случае нужны.


## Часть 2:

Для начала надо явным образом отделить друг от друга сущности «облака» и сущности локального хранилища. В целом, объекты должны свободно перемещаться между типами хранилищ. Не только на уровне «файлов», но и на уровне абстрактных сущностей.

Следующая мысль была в том, что необходимо создать класс хранилища с набором операций, позволяющих создать древовидную структуру файлов и папок. При этом также должны быть реализованы классы folder и file, объединённые абстрактным классом storage_element, в котором хранятся общие для обоих классов методы.

К сожалению, на реализацию всего задуманного нужно больше времени, чем отведено в рамках задания.

Вывод: я больше думаю над кодом, чем его пишу. Особенно, когда сталкиваюсь с новыми концепциями. Для того, чтобы бороться с этим, ввёл себе регулярные практики глубокой работы. Пока что получается минимум час выделять ежедневно на это. Перехожу к стабильным двум часам.