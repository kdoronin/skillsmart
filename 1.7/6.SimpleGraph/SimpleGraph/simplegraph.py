class Vertex:

    def __init__(self, val):
        self.Value = val
        self.Hit = False


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
            self.vertex[i] = Vertex(v)
        else:
            return False
        # ваш код добавления новой вершины
        # с значением value
        # в свободное место массива vertex

        # здесь и далее, параметры v -- индекс вершины

    # в списке  vertex
    def RemoveVertex(self, v):
        # ваш код удаления вершины со всеми её рёбрами
        if v < self.max_vertex:
            self.vertex.pop(v)
            self.m_adjacency.pop(v)
            self.vertex.append(None)
            self.m_adjacency.append([0] * self.max_vertex)

    def IsEdge(self, v1, v2):
        # True если есть ребро между вершинами v1 и v2
        if v1 < self.max_vertex and v2 < self.max_vertex:
            if self.m_adjacency[v1][v2] == 1 and self.m_adjacency[v2][v1] == 1:
                return True
        return False

    def AddEdge(self, v1, v2):
        # добавление ребра между вершинами v1 и v2
        if v1 < self.max_vertex and v2 < self.max_vertex:
            self.m_adjacency[v1][v2] = 1
            self.m_adjacency[v2][v1] = 1

    def RemoveEdge(self, v1, v2):
        if v1 < self.max_vertex and v2 < self.max_vertex:
            self.m_adjacency[v1][v2] = 0
            self.m_adjacency[v2][v1] = 0
        # удаление ребра между вершинами v1 и v2

    def DepthFirstSearch(self, VFrom, VTo):
        for i in range(self.max_vertex):
            self.vertex[i].Hit = False
        result_index_stack = []
        result_index_stack = self.dfs_step(VFrom, VTo, result_index_stack)
        result_stack = []
        if result_index_stack:
            for i in result_index_stack:
                result_stack.append(self.vertex[i])
        return result_stack

    def dfs_step(self, VCurrent, VTo, result_stack):
        self.vertex[VCurrent].Hit = True
        result_stack.append(VCurrent)
        if self.m_adjacency[VCurrent][VTo] == 1:
            result_stack.append(VTo)
            return result_stack
        for i in range(self.max_vertex):
            if self.m_adjacency[VCurrent][i] == 1 and self.vertex[i].Hit is False:
                result_stack = self.dfs_step(i, VTo, result_stack)
                if result_stack:
                    last_element = result_stack.pop()
                    if last_element == VTo:
                        result_stack.append(last_element)
                        return result_stack
                    else:
                        result_stack.append(last_element)
        if not result_stack:
            return result_stack
        result_stack.pop()
        if result_stack:
            return self.dfs_step(result_stack.pop(), VTo, result_stack)
        return result_stack

