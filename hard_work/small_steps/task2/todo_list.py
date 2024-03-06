## Система управления задачами (Todo List)
## Разработайте простую систему управления задачами, которая позволяет добавлять задачи, удалять их и отмечать выполненные.


class ToDoList:
    def __init__(self):
        self.tasks = {}

    def add_task(self, task):
        self.tasks[task] = False

    def delete_task(self, task):
        self.tasks.pop(task)

    def complete_task(self, task):
        self.tasks[task] = True

    def edit_task(self, task, new_task):
        self.tasks[new_task] = self.tasks.pop(task)