import unittest
from SimpleGraph.simplegraph import SimpleGraph
from SimpleGraph.simplegraph import Vertex
from SimpleGraph.simplegraph import Queue
from SimpleGraph.simplegraph import SimpleTreeNode
from SimpleGraph.simplegraph import  SimpleTree


class testSimpleGraph(unittest.TestCase):
    def setUp(self):
        self.one_element_graph = SimpleGraph(1)
        self.five_elements_graph = SimpleGraph(5)
        self.vertex_a = Vertex('A')
        self.vertex_b = Vertex('B')
        self.vertex_c = Vertex('C')
        self.vertex_d = Vertex('D')
        self.vertex_e = Vertex('E')
        self.one_element_graph.AddVertex('A')
        self.five_elements_graph.AddVertex('A')
        self.five_elements_graph.AddVertex('B')
        self.five_elements_graph.AddVertex('C')
        self.five_elements_graph.AddVertex('D')
        self.five_elements_graph.AddVertex('E')


    def test_add_vertex(self):
        self.assertFalse(self.one_element_graph.AddVertex(self.vertex_b))
        self.assertEqual(self.one_element_graph.vertex[0].Value, self.vertex_a.Value)
        self.assertEqual(self.five_elements_graph.vertex[0].Value, self.vertex_a.Value)
        self.assertEqual(self.five_elements_graph.vertex[1].Value, self.vertex_b.Value)
        for i in range(self.five_elements_graph.max_vertex):
            for j in range(self.five_elements_graph.max_vertex):
                self.assertEqual(self.five_elements_graph.m_adjacency[i][j], 0)

    def test_add_edge(self):
        self.assertEqual(self.five_elements_graph.m_adjacency[0][1], 0)
        self.assertEqual(self.five_elements_graph.m_adjacency[1][0], 0)
        self.five_elements_graph.AddEdge(0, 1)
        self.assertEqual(self.five_elements_graph.m_adjacency[0][1], 1)
        self.assertEqual(self.five_elements_graph.m_adjacency[1][0], 1)
        self.assertEqual(self.five_elements_graph.m_adjacency[0][2], 0)
        self.assertEqual(self.five_elements_graph.m_adjacency[2][0], 0)
        self.five_elements_graph.AddEdge(2, 0)
        self.assertEqual(self.five_elements_graph.m_adjacency[0][2], 1)
        self.assertEqual(self.five_elements_graph.m_adjacency[2][0], 1)
        self.assertEqual(self.five_elements_graph.m_adjacency[3][3], 0)
        self.five_elements_graph.AddEdge(3, 3)
        self.assertEqual(self.five_elements_graph.m_adjacency[3][3], 1)

    def test_remove_edge(self):
        self.one_element_graph.AddEdge(0, 0)
        self.assertEqual(self.one_element_graph.m_adjacency[0][0], 1)
        self.one_element_graph.RemoveEdge(0, 0)
        self.assertEqual(self.one_element_graph.m_adjacency[0][0], 0)
        self.five_elements_graph.AddEdge(0, 4)
        self.assertEqual(self.five_elements_graph.m_adjacency[0][4], 1)
        self.assertEqual(self.five_elements_graph.m_adjacency[4][0], 1)
        self.five_elements_graph.RemoveEdge(0, 4)
        self.assertEqual(self.five_elements_graph.m_adjacency[0][4], 0)
        self.assertEqual(self.five_elements_graph.m_adjacency[4][0], 0)

    def test_remove_vertex(self):
        self.one_element_graph.AddEdge(0, 0)
        self.assertEqual(self.one_element_graph.m_adjacency[0][0], 1)
        self.assertEqual(self.one_element_graph.vertex[0].Value, self.vertex_a.Value)
        self.one_element_graph.RemoveVertex(0)
        self.assertEqual(self.one_element_graph.m_adjacency[0][0], 0)
        self.assertIsNone(self.one_element_graph.vertex[0])

    def test_is_edge(self):
        self.assertFalse(self.one_element_graph.IsEdge(0, 0))
        self.one_element_graph.AddEdge(0, 0)
        self.assertTrue(self.one_element_graph.IsEdge(0, 0))
        self.assertFalse(self.five_elements_graph.IsEdge(0, 1))
        self.five_elements_graph.AddEdge(0, 1)
        self.assertTrue(self.five_elements_graph.IsEdge(0, 1))

    def test_dfs(self):
        self.five_elements_graph.AddEdge(0, 1)
        self.five_elements_graph.AddEdge(2, 0)
        self.five_elements_graph.AddEdge(2, 3)
        self.five_elements_graph.AddEdge(0, 3)
        self.five_elements_graph.AddEdge(1, 3)
        self.five_elements_graph.AddEdge(1, 4)
        self.five_elements_graph.AddEdge(3, 4)
        self.five_elements_graph.AddEdge(3, 3)
        self.five_elements_graph.RemoveEdge(1, 4)
        first_way = self.five_elements_graph.DepthFirstSearch(0, 4)
        good_way = [self.five_elements_graph.vertex[0], self.five_elements_graph.vertex[1], self.five_elements_graph.vertex[3], self.five_elements_graph.vertex[4]]
        self.assertEqual(first_way, good_way)
        self.five_elements_graph.RemoveEdge(3, 4)
        second_way = self.five_elements_graph.DepthFirstSearch(2, 4)
        good_way = []
        self.assertEqual(second_way, good_way)

    def test_bfs(self):
        self.five_elements_graph.AddEdge(0, 1)
        self.five_elements_graph.AddEdge(2, 0)
        self.five_elements_graph.AddEdge(2, 3)
        self.five_elements_graph.AddEdge(0, 3)
        self.five_elements_graph.AddEdge(1, 3)
        self.five_elements_graph.AddEdge(1, 4)
        self.five_elements_graph.AddEdge(3, 4)
        self.five_elements_graph.AddEdge(3, 3)
        self.five_elements_graph.RemoveEdge(1, 4)
        first_way = self.five_elements_graph.BreadthFirstSearch(0, 4)
        good_way = [self.five_elements_graph.vertex[0], self.five_elements_graph.vertex[3], self.five_elements_graph.vertex[4]]
        self.assertEqual(first_way, good_way)
        self.five_elements_graph.RemoveEdge(3, 4)
        second_way = self.five_elements_graph.DepthFirstSearch(2, 4)
        good_way = []
        self.assertEqual(second_way, good_way)

    def test_weak_vertices(self):
        self.five_elements_graph.AddEdge(0, 1)
        self.five_elements_graph.AddEdge(2, 0)
        self.five_elements_graph.AddEdge(2, 3)
        self.five_elements_graph.AddEdge(0, 3)
        self.five_elements_graph.AddEdge(1, 3)
        self.five_elements_graph.AddEdge(1, 4)
        self.five_elements_graph.AddEdge(3, 4)
        self.five_elements_graph.AddEdge(3, 3)
        first_array = self.five_elements_graph.WeakVertices()
        first_correct = []
        self.assertEqual(first_array, first_correct)
        self.five_elements_graph.RemoveEdge(3, 4)
        second_array = self.five_elements_graph.WeakVertices()
        second_correct = [self.five_elements_graph.vertex[4]]
        self.assertEqual(second_array, second_correct)
        self.five_elements_graph.RemoveEdge(1, 3)
        self.five_elements_graph.AddEdge(3, 4)
        third_array = self.five_elements_graph.WeakVertices()
        self.assertEqual(len(third_array), 2)
        self.assertTrue(self.five_elements_graph.vertex[4] in third_array)
        self.assertTrue(self.five_elements_graph.vertex[1] in third_array)


