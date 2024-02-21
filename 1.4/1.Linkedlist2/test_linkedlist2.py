import unittest
from linkedlist2.linkedlist2 import Node
from linkedlist2.linkedlist2 import LinkedList2

class TestFind(unittest.TestCase):
    def test_find(self):
        n1 = Node(1)
        n2 = Node(2)
        n3 = Node(3)
        find_list = LinkedList2()
        self.assertIsNone(find_list.find(1))
        find_list.add_in_tail(n1)
        self.assertEqual(find_list.find(1), n1)
        find_list.add_in_tail(n2)
        self.assertEqual(find_list.find(2), n2)
        find_list.add_in_tail(n3)
        self.assertEqual(find_list.find(3), n3)

    def test_find_all(self):
        n1 = Node(1)
        n2 = Node(2)
        n3 = Node(3)
        n4 = Node(1)
        n5 = Node(1)
        find_list = LinkedList2()
        self.assertEqual(find_list.find_all(1), [])
        find_list.add_in_tail(n1)
        self.assertEqual(find_list.find_all(1), [n1])
        find_list.add_in_tail(n4)
        find_list.add_in_tail(n2)
        find_list.add_in_tail(n5)
        find_list.add_in_tail(n3)
        result_array = list()
        result_array.append(n1)
        result_array.append(n4)
        result_array.append(n5)
        self.assertEqual(find_list.find_all(1), result_array)
        self.assertEqual(find_list.find_all(3), [n3])

    def test_delete(self):
        n1 = Node(1)
        n2 = Node(2)
        n3 = Node(3)
        n4 = Node(1)
        n5 = Node(1)
        delete_list1 = LinkedList2()
        delete_list2 = LinkedList2()
        delete_list1.add_in_tail(n1)
        delete_list1.delete(1, False)
        self.assertTrue(compare_lists(delete_list1, delete_list2))
        delete_list1.add_in_tail(n1)
        delete_list1.add_in_tail(Node(2))
        delete_list2.add_in_tail(n4)
        delete_list1.delete(2)
        self.assertTrue(compare_lists(delete_list1, delete_list2))

def compare_lists(list1: LinkedList2, list2: LinkedList2):
    node1 = list1.head
    node2 = list2.head
    if list1.len() == list2.len():
        while node1 is not None:
            if node1.value != node2.value:
                return False
            node1 = node1.next
            node2 = node2.next
    else:
        return False
    return True



if __name__ == '__main__':
    unittest.main()