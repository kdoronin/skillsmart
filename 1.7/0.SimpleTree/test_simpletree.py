import unittest
from SimpleTree.simpletree import SimpleTreeNode
from SimpleTree.simpletree import SimpleTree

class testSimpleTree(unittest.TestCase):
    def setUp(self):
        self.node = [None]*11
        for i in range(10):
            self.node[i+1] = SimpleTreeNode(i+1, None)
        self.myTree = SimpleTree(self.node[1])
        self.myTree.AddChild(self.node[1], self.node[2])
        self.myTree.AddChild(self.node[1], self.node[3])
        self.myTree.AddChild(self.node[3], self.node[4])
        self.myTree.AddChild(self.node[2], self.node[5])
        self.myTree.AddChild(self.node[2], self.node[7])
        self.myTree.AddChild(self.node[1], self.node[6])
        self.myTree.AddChild(self.node[6], self.node[8])
        self.myTree.AddChild(self.node[8], self.node[9])
        self.myTree.AddChild(self.node[8], self.node[10])

    def test_even_trees(self):
        resultlist = self.myTree.EvenTrees()
        count = self.myTree.Count()
        self.assertEqual(resultlist[0], self.node[1])
        self.assertEqual(resultlist[1], self.node[3])
        self.assertEqual(resultlist[2], self.node[1])
        self.assertEqual(resultlist[3], self.node[6])
