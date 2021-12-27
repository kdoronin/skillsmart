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
