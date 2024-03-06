import unittest

from todo_list import ToDoList


class TestToDoList(unittest.TestCase):
    def test_add_task(self):
        todolist = ToDoList()
        todolist.add_task('task1')
        todolist.add_task('task2')
        todolist.add_task('task3')
        self.assertEqual(todolist.tasks, {'task1': False, 'task2': False, 'task3': False})

    def test_delete_task(self):
        todolist = ToDoList()
        todolist.add_task('task1')
        todolist.add_task('task2')
        todolist.add_task('task3')
        todolist.delete_task('task2')
        self.assertEqual(todolist.tasks, {'task1': False, 'task3': False})

    def test_mark_done(self):
        todolist = ToDoList()
        todolist.add_task('task1')
        todolist.add_task('task2')
        todolist.add_task('task3')
        todolist.complete_task('task2')
        self.assertEqual(todolist.tasks, {'task1': False, 'task2': True, 'task3': False})

    def test_edit_task(self):
        todolist = ToDoList()
        todolist.add_task('task1')
        todolist.add_task('task2')
        todolist.add_task('task3')
        todolist.edit_task('task2', 'task4')
        self.assertEqual(todolist.tasks, {'task1': False, 'task4': False, 'task3': False})
