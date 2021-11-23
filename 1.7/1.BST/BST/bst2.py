class BSTNode:

    def __init__(self, key, val, parent):
        self.NodeKey = key  # ключ узла
        self.NodeValue = val  # значение в узле
        self.Parent = parent  # родитель или None для корня
        self.LeftChild = None  # левый потомок
        self.RightChild = None  # правый потомок


class BSTFind:  # промежуточный результат поиска

    def __init__(self, node, haskey, toleft):
        self.Node = node  # None если
        # в дереве вообще нету узлов

        self.NodeHasKey = haskey  # True если узел найден
        self.ToLeft = toleft  # True, если родительскому узлу надо
        # добавить новый узел левым потомком


class BST:

    def __init__(self, node):
        self.Root = node  # корень дерева, или None
        if node is not None:
            node.Parent = None
            self.Number = 1
        else:
            self.Number = 0

    def FindNodeByKey(self, key):
        # ищем в дереве узел и сопутствующую информацию по ключу
        return self.find_step(key, self.Root)  # возвращает BSTFind

    def find_step(self, key, node):
        if node is None:
            return BSTFind(node, False, False)
        elif node.NodeKey == key:
            return BSTFind(node, True, False)
        elif key > node.NodeKey:
            if node.RightChild is not None:
                return self.find_step(key, node.RightChild)
            else:
                return BSTFind(node, False, False)
        else:
            if node.LeftChild is not None:
                return self.find_step(key, node.LeftChild)
            else:
                return BSTFind(node, False, True)

    def AddKeyValue(self, key, val):
        # добавляем ключ-значение в дерево
        parent_node = self.FindNodeByKey(key)
        if parent_node.NodeHasKey:
            return False  # если ключ уже есть
        elif parent_node.Node is None:
            node = BSTNode(key, val, parent_node.Node)
            self.Root = node
            self.Number += 1
            return True
        else:
            node = BSTNode(key, val, parent_node.Node)
            if parent_node.ToLeft:
                parent_node.Node.LeftChild = node
            else:
                parent_node.Node.RightChild = node
            self.Number += 1
            return True

    def FinMinMax(self, FromNode, FindMax):
        # ищем максимальный/минимальный ключ в поддереве
        # возвращается объект типа BSTNode
        if self.Root is None:
            return self.Root
        else:
            return self.find_max_step(FromNode, FindMax)

    def find_max_step(self, node, max):
        if max:
            if node.RightChild is None:
                return node
            else:
                return self.find_max_step(node.RightChild, max)
        else:
            if node.LeftChild is None:
                return node
            else:
                return self.find_max_step(node.LeftChild, max)

    def DeleteNodeByKey(self, key):
        # удаляем узел по ключу
        find_node = self.FindNodeByKey(key)
        if find_node.NodeHasKey is False:
            return False  # если узел не найден
        else:
            node = find_node.Node
            if node.Parent is not None and node == node.Parent.LeftChild:
                left = True
            else:
                left = False
            if node.RightChild is not None and node.LeftChild is not None:
                min_node = self.FinMinMax(node.RightChild, False)
                if min_node == node.RightChild:
                    if node.Parent is not None:
                        if left:
                            node.Parent.LeftChild = node.RightChild
                            node.RightChild.Parent = node.Parent
                            node.RightChild.LeftChild = node.LeftChild
                        else:
                            node.Parent.RightChild = node.RightChild
                            node.RightChild.Parent = node.Parent
                            node.RightChild.LeftChild = node.LeftChild
                    else:
                        node.RightChild.Parent = None
                        self.Root = node.RightChild
                    self.Number -= 1
                    return True
                else:
                    new_node = self.delete_min_leaf(min_node, True)
                    self.insert_leaf(new_node, node.Parent, left)
                    self.Number -= 1
                    return True
            else:
                self.simple_delete_node(node, node.Parent, left)
                self.Number -= 1
                return True

    def simple_delete_node(self, node, parent, left):
        if node.RightChild is not None:
            if parent is not None:
                if left:
                    parent.LeftChild = node.RightChild
                    node.RightChild.Parent = parent
                else:
                    parent.RightChild = node.RightChild
                    node.RightChild.Parent = parent
            else:
                node.RightChild.Parent = None
                self.Root = node.RightChild
        elif node.LeftChild is not None:
            if parent is not None:
                if left:
                    parent.LeftChild = node.LeftChild
                    node.LeftChild.Parent = parent
                else:
                    parent.RightChild = node.LeftChild
                    node.LeftChild.Parent = parent
            else:
                node.LeftChild.Parent = None
                self.Root = node.LeftChild
        else:
            if parent is not None:
                if left:
                    parent.LeftChild = None
                else:
                    parent.RightChild = None
            else:
                self.Root = None

    def insert_node(self, node, parent, left):
        if parent is None:
            node.Parent = None
            self.Root = node
            return node
        else:
            if left:
                node.Parent = parent
                parent.LeftChild = node
                return node
            else:
                node.Parent = parent
                parent.LeftChild = node
                return node

    def insert_leaf(self, node, parent, left):
        if parent is None:
            node.LeftChild = self.Root.LeftChild
            node.RightChild = self.Root.RightChild
            node.RightChild.Parent = node
            node.LeftChild.Parent = node
            self.Root = node
            return node
        else:
            if left:
                node.LeftChild = parent.LeftChild.LeftChild
                node.RightChild = parent.LeftChild.RightChild
                node.LeftChild.Parent = node
                node.RightChild.Parent = node
                node.Parent = parent
                parent.LeftChild = node
                return node
            else:
                node.LeftChild = parent.RightChild.LeftChild
                node.RightChild = parent.RightChild.RightChild
                node.LeftChild.Parent = node
                node.RightChild.Parent = node
                node.Parent = parent
                parent.LeftChild = node
                return node

    def delete_min_leaf(self, node, left):
        if node.LeftChild is not None:
            return False
        elif node.RightChild is not None:
            if left:
                node.Parent.LeftChild = node.RightChild
                node.RightChild.Parent = node.Parent
                node.Parent = None
                return node
            else:
                node.Parent.RightChild = node.RightChild
                node.RightChild.Parent = node.Parent
                node.Parent = None
                return node
        elif node.Parent is None:
            node.Parent = None
            self.Root = None
            return node
        else:
            if left:
                node.Parent.LeftChild = None
                node.Parent = None
                return node
            else:
                node.Parent.RightChild = None
                node.Parent = None
                return node

    def Count(self):
        return self.Number  # количество узлов в дереве