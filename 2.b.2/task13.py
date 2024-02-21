# метод публичен в родительском классе А и публичен в его потомке B
class A:
    def my_method(self):
        print("Public method in A")


class B(A):
    def my_method(self):
        print("Public method in B")


# метод публичен в родительском классе А и скрыт в его потомке B
class A:
    def my_method(self):
        print("Public method in A")


class B(A):
    def __my_method(self):
        print("Private method in B")

    def test_private_method(self):
        self.__my_method()


T = B()
T.test_private_method()  # Выведет "Private method in B"


# метод скрыт в родительском классе А и публичен в его потомке B
# напрямую открыть метод нельзя, но в потомке можно вызвать приватный метод из публичного
class A:
    def __my_method(self):
        print("Private method in A")


class B(A):
    def my_method_public(self):
        self._A__my_method()


c = B()

c.my_method_public()

# метод скрыт в родительском классе А и скрыт в его потомке B

class A:
    def __my_method(self):
        print("Public method in A")


class B(A):
    def __my_method(self):
        print("Public method in B")