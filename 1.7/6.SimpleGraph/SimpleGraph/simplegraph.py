class Vertex:

    def __init__(self, val):
        self.Value = val
        self.Hit = False
        self.leaf = None


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

    def BreadthFirstSearch(self, VFrom, VTo):
        for i in range(self.max_vertex):
            self.vertex[i].Hit = False
            self.vertex[i].leaf = None
        worked_queue = Queue()
        self.vertex[VFrom].leaf = SimpleTreeNode(self.vertex[VFrom], None)
        worked_tree = SimpleTree(self.vertex[VFrom].leaf)
        if self.bfs_step(VFrom, VTo, worked_queue, worked_tree):
            return self.send_array(VTo)
        return []

    def send_array(self, VTo):
        finded = self.vertex[VTo].leaf
        result = []
        while finded is not None:
            result.append(finded.NodeValue)
            finded = finded.Parent
        result.reverse()
        return result


    def bfs_step(self, VCurrent, VTo, worked_queue, worked_tree):
        self.vertex[VCurrent].Hit = True
        if self.m_adjacency[VCurrent][VTo] == 1:
            self.vertex[VTo].leaf = SimpleTreeNode(self.vertex[VTo], self.vertex[VCurrent].leaf)
            worked_tree.AddChild(self.vertex[VCurrent].leaf, self.vertex[VTo].leaf)
            return True
        for i in range(self.max_vertex):
            if self.m_adjacency[VCurrent][i] == 1 and self.vertex[i].Hit is False:
                self.vertex[i].Hit = True
                self.vertex[i].leaf = SimpleTreeNode(self.vertex[i], self.vertex[VCurrent].leaf)
                worked_tree.AddChild(self.vertex[VCurrent].leaf, self.vertex[i].leaf)
                worked_queue.enqueue(i)
        if worked_queue.size() > 0:
            return self.bfs_step(worked_queue.dequeue(), VTo, worked_queue, worked_tree)
        return False

    def WeakVertices(self):
        collect_list = []
        output_list = []
        for i in range(self.max_vertex):
            collect_list.append([])
            for j in range(self.max_vertex):
                if self.m_adjacency[i][j] == 1 and i != j and self.vertex[j] not in output_list:
                    collect_list[i].append(j)
            self.check_vertexes(self.vertex[i], collect_list[i], output_list)
        return output_list

    def check_vertexes(self, vertex, collect_list, output_list):
        for i in collect_list:
            for j in collect_list:
                if self.m_adjacency[i][j] == 1 and i != j:
                    return
        output_list.append(vertex)
        return

class Queue:
    def __init__(self):
        self.queue = []
        # инициализация хранилища данных

    def enqueue(self, item):
        self.queue.append(item)
        # вставка в хвост

    def dequeue(self):
        # выдача из головы
        if self.size() > 0:
            return self.queue.pop(0)
        else:
            return None # если очередь пустая

    def size(self):
        return len(self.queue)

    def rotateQueue(self, N: int):
        for i in range(N):
            self.enqueue(self.dequeue())
            i += 1

class SimpleTreeNode:

    def __init__(self, val, parent):
        self.NodeValue = val  # значение в узле
        self.Parent = parent  # родитель или None для корня
        self.Children = []  # список дочерних узлов


class SimpleTree:

    def __init__(self, root):
        self.Root = root  # корень, может быть None

    def AddChild(self, ParentNode, NewChild):
        ParentNode.Children.append(NewChild)
        NewChild.Parent = ParentNode
        # ваш код добавления нового дочернего узла существующему ParentNode

    def DeleteNode(self, NodeToDelete):
        parent = NodeToDelete.Parent
        parent.Children.remove(NodeToDelete)
        # ваш код удаления существующего узла NodeToDelete

    def GetAllNodes(self):
        # ваш код выдачи всех узлов дерева в определённом порядке
        allnodes = []
        return self.get_node(self.Root, allnodes)

    def get_node(self, current_node, all_nodes):
        all_nodes.append(current_node)
        if current_node.Children:
            for child in current_node.Children:
                all_nodes = self.get_node(child, all_nodes)
        return all_nodes

    def FindNodesByValue(self, val):
        # ваш код поиска узлов по значению
        all_nodes = []
        return self.find_node(self.Root, val, all_nodes)

    def find_node(self, current_node, val, all_nodes):
        if current_node.NodeValue == val:
            all_nodes.append(current_node)
        if current_node.Children:
            for child in current_node.Children:
                all_nodes = self.find_node(child, val, all_nodes)
        return all_nodes

    def MoveNode(self, OriginalNode, NewParent):
        # ваш код перемещения узла вместе с его поддеревом --
        # в качестве дочернего для узла NewParent
        old_parent = OriginalNode.Parent
        old_parent.Children.remove(OriginalNode)
        NewParent.Children.append(OriginalNode)
        OriginalNode.Parent = NewParent

    def Count(self):
        # количество всех узлов в дереве
        num = 0
        return self.count_node(self.Root, num)

    def count_node(self, node, num):
        if node is not None:
            num += 1
        if node.Children:
            for child in node.Children:
                num = self.count_node(child, num)
        return num

    def LeafCount(self):
        # количество листьев в дереве
        num = 0
        return self.leaf_num(self.Root, num)

    def leaf_num(self, node, num):
        if node.Children:
            for child in node.Children:
                num = self.leaf_num(child, num)
        else:
            num += 1
        return num

    def EvenTrees(self):
        mylist = []
        self.even_step(self.Root, mylist)
        return mylist

    def even_step(self, node, mylist):
        if node.Children:
            for i in node.Children:
                node_count = self.count_node(i, 0)
                if node_count % 2 == 0:
                    mylist.append(node)
                    mylist.append(i)
                    if node_count > 4:
                        self.even_step(i, mylist)
                elif node_count > 3:
                    self.even_step(i, mylist)
