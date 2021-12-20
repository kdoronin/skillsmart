import unittest
from SimpleGraph.simplegraph import SimpleGraph
from SimpleGraph.simplegraph import Vertex


class testSimpleGraph(unittest.TestCase):
    def setUp(self):
        self.one_element_graph = SimpleGraph(1)
        self.five_elements_graph = SimpleGraph(5)
        self.vertex_a = Vertex('A')
        self.vertex_b = Vertex('B')
        self.vertex_c = Vertex('C')
        self.vertex_d = Vertex('D')
        self.vertex_e = Vertex('E')
        self.one_element_graph.AddVertex(self.vertex_a)
        self.five_elements_graph.AddVertex(self.vertex_a)
        self.five_elements_graph.AddVertex(self.vertex_b)
        self.five_elements_graph.AddVertex(self.vertex_c)
        self.five_elements_graph.AddVertex(self.vertex_d)
        self.five_elements_graph.AddVertex(self.vertex_e)


    def test_add_vertex(self):
        self.assertFalse(self.one_element_graph.AddVertex(self.vertex_b))
        self.assertEqual(self.one_element_graph.vertex[0], self.vertex_a)
        self.assertEqual(self.five_elements_graph.vertex[0], self.vertex_a)
        self.assertEqual(self.five_elements_graph.vertex[1], self.vertex_b)
        for i in range(self.five_elements_graph.max_vertex):
            for j in range(self.five_elements_graph.max_vertex):
                self.assertEqual(self.five_elements_graph.m_adjacency[i][j], 0)

    def test_add_edge(self):
        self.assertEqual(self.five_elements_graph.m_adjacency[0][1], 0)
        self.assertEqual(self.five_elements_graph.m_adjacency[1][0], 0)
        self.five_elements_graph.AddEdge(self.vertex_a, self.vertex_b)
        self.assertEqual(self.five_elements_graph.m_adjacency[0][1], 1)
        self.assertEqual(self.five_elements_graph.m_adjacency[1][0], 1)
        self.assertEqual(self.five_elements_graph.m_adjacency[0][2], 0)
        self.assertEqual(self.five_elements_graph.m_adjacency[2][0], 0)
        self.five_elements_graph.AddEdge(self.vertex_c, self.vertex_a)
        self.assertEqual(self.five_elements_graph.m_adjacency[0][2], 1)
        self.assertEqual(self.five_elements_graph.m_adjacency[2][0], 1)
        self.assertEqual(self.five_elements_graph.m_adjacency[3][3], 0)
        self.five_elements_graph.AddEdge(self.vertex_d, self.vertex_d)
        self.assertEqual(self.five_elements_graph.m_adjacency[3][3], 1)

    def test_remove_edge(self):
        self.one_element_graph.AddEdge(self.vertex_a, self.vertex_a)
        self.assertEqual(self.one_element_graph.m_adjacency[0][0], 1)
        self.one_element_graph.RemoveEdge(self.vertex_a, self.vertex_a)
        self.assertEqual(self.one_element_graph.m_adjacency[0][0], 0)
        self.five_elements_graph.AddEdge(self.vertex_a, self.vertex_e)
        self.assertEqual(self.five_elements_graph.m_adjacency[0][4], 1)
        self.assertEqual(self.five_elements_graph.m_adjacency[4][0], 1)
        self.five_elements_graph.RemoveEdge(self.vertex_a, self.vertex_e)
        self.assertEqual(self.five_elements_graph.m_adjacency[0][4], 0)
        self.assertEqual(self.five_elements_graph.m_adjacency[4][0], 0)

    def test_remove_vertex(self):
        self.one_element_graph.AddEdge(self.vertex_a, self.vertex_a)
        self.assertEqual(self.one_element_graph.m_adjacency[0][0], 1)
        self.assertEqual(self.one_element_graph.vertex[0], self.vertex_a)
        self.one_element_graph.RemoveVertex(self.vertex_a)
        self.assertEqual(self.one_element_graph.m_adjacency[0][0], 0)
        self.assertIsNone(self.one_element_graph.vertex[0])

    def test_is_edge(self):
        self.assertFalse(self.one_element_graph.IsEdge(self.vertex_a, self.vertex_a))
        self.one_element_graph.AddEdge(self.vertex_a, self.vertex_a)
        self.assertTrue(self.one_element_graph.IsEdge(self.vertex_a, self.vertex_a))
        self.assertFalse(self.five_elements_graph.IsEdge(self.vertex_a, self.vertex_b))
        self.five_elements_graph.AddEdge(self.vertex_a, self.vertex_b)
        self.assertTrue(self.five_elements_graph.IsEdge(self.vertex_a, self.vertex_b))