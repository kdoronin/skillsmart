import unittest
from BalancedBST.balancedbst import BalancedBST, BSTNode


class testBalancedBST(unittest.TestCase):
    def setUp(self):
        self.first_list = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15]
        self.second_list = [3, 2, 1, 4]
        self.second_control_list = [2, 1, 3]
        self.third_list = [8, 4, 12, 2, 6, 10, 14, 1, 3, 5, 7, 9, 11, 13, 15]
        self.four_list = [1, 4, 8, 7, 5, 2, 3, 6, 12, 10, 11, 13, 15, 14, 9]
        self.first_bst = BalancedBST()
        self.first_bst.GenerateTree(self.first_list)
        self.second_bst = BalancedBST()
        self.second_bst.GenerateTree(self.four_list)
        self.third_bst = BalancedBST()
        self.third_bst.GenerateTree(self.second_list)
        self.wrong_bst = BalancedBST()
        wrong_root = BSTNode(3, None)
        self.wrong_bst.Root = wrong_root
        wrong_left = BSTNode(2, wrong_root)
        wrong_left.Level = 1
        self.wrong_bst.Root.LeftChild = wrong_left
        wrong_right = BSTNode(4, wrong_root)
        wrong_right.Level = 1
        self.wrong_bst.Root.RightChild = wrong_right
        wrong_right_child = BSTNode(5, wrong_right)
        wrong_right_child.Level = 2
        self.wrong_bst.Root.RightChild.RightChild = wrong_right_child
        wrong_right_child_child = BSTNode(6, wrong_right_child)
        wrong_right_child_child.Level = 3
        self.wrong_bst.Root.RightChild.RightChild.RightChild = wrong_right_child_child


    def test_generate_balancedbst(self):
        self.assertEqual(self.first_bst.Root.NodeKey, 8)
        self.assertEqual(self.first_bst.Root.LeftChild.NodeKey, 4)
        self.assertEqual(self.first_bst.Root.RightChild.NodeKey, 12)
        self.assertEqual(self.second_bst.Root.NodeKey, 8)
        self.assertEqual(self.second_bst.Root.LeftChild.NodeKey, 4)
        self.assertEqual(self.second_bst.Root.RightChild.NodeKey, 12)
        self.assertEqual(self.third_bst.Root.NodeKey, 2)
        self.assertEqual(self.third_bst.Root.LeftChild.NodeKey, 1)
        self.assertEqual(self.third_bst.Root.RightChild.NodeKey, 3)


    def test_is_balanced(self):
        self.assertTrue(self.first_bst.IsBalanced(self.first_bst.Root))
        self.assertTrue(self.second_bst.IsBalanced(self.second_bst.Root))
        self.assertTrue(self.third_bst.IsBalanced(self.third_bst.Root))
        self.assertFalse(self.wrong_bst.IsBalanced(self.wrong_bst.Root))

