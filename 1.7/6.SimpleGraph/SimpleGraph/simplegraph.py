class Vertex:

    def __init__(self, val):
        self.Value = val


class SimpleGraph:

    def __init__(self, size):
        self.max_vertex = size
        self.m_adjacency = [[0] * size for _ in range(size)]
        self.vertex = [None] * size

    def AddVertex(self, v):
        if self.vertex[-1] is None:
            i = 0
            while self.vertex[i] is not None and i < len(self.vertex):
                i += 1
            self.vertex[i] = v
        else:
            return False
        # ваш код добавления новой вершины
        # с значением value
        # в свободное место массива vertex

        # здесь и далее, параметры v -- индекс вершины

    # в списке  vertex
    def RemoveVertex(self, v):
        # ваш код удаления вершины со всеми её рёбрами
        try:
            vertex_index = self.vertex.index(v)
        except:
            return False
        self.vertex.pop(vertex_index)
        self.m_adjacency.pop(vertex_index)
        self.vertex.append(None)
        self.m_adjacency.append([0] * self.max_vertex)

    def IsEdge(self, v1, v2):
        # True если есть ребро между вершинами v1 и v2
        try:
            index1 = self.vertex.index(v1)
            index2 = self.vertex.index(v2)
        except:
            return False
        if self.m_adjacency[index1][index2] == 1 and self.m_adjacency[index2][index1] == 1:
            return True
        else:
            return False

    def AddEdge(self, v1, v2):
        # добавление ребра между вершинами v1 и v2
        try:
            index1 = self.vertex.index(v1)
            index2 = self.vertex.index(v2)
        except:
            return False
        self.m_adjacency[index1][index2] = 1
        self.m_adjacency[index2][index1] = 1

    def RemoveEdge(self, v1, v2):
        try:
            index1 = self.vertex.index(v1)
            index2 = self.vertex.index(v2)
        except:
            return False
        self.m_adjacency[index1][index2] = 0
        self.m_adjacency[index2][index1] = 0
        # удаление ребра между вершинами v1 и v2