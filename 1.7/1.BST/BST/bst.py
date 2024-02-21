class BSTNode:

    def __init__(self, key, val, parent):
        self.NodeKey = key  # ключ узла
        self.NodeValue = val  # значение в узле
        self.Parent = parent  # родитель или None для корня
        self.LeftChild = None  # левый потомок
        self.RightChild = None  # правый потомок


class BSTFind:  # промежуточный результат поиска

    def __init__(self):
        self.Node = None  # None если
        # в дереве вообще нету узлов

        self.NodeHasKey = False  # True если узел найден
        self.ToLeft = False  # True, если родительскому узлу надо
        # добавить новый узел левым потомком


class BST:

    def __init__(self, node):
        self.Root = node  # корень дерева, или None
        if node is None:
            self.Number = 0
        else:
            self.Number = 1

    def FindNodeByKey(self, key):
        # ищем в дереве узел и сопутствующую информацию по ключу
        return self.check_current_node(self.Root, key)  # возвращает BSTFind

    def check_current_node(self, node, key):
        if node is None:
            return_node = BSTFind()
            return return_node
        elif node.NodeKey == key:
            return_node = BSTFind()
            return_node.Node = node
            return_node.NodeHasKey = True
            return return_node
        elif key > node.NodeKey:
            if node.RightChild is not None:
                return self.check_current_node(node.RightChild, key)
            else:
                return_node = BSTFind()
                return_node.Node = node
                return return_node
        else:
            if node.LeftChild is not None:
                return self.check_current_node(node.LeftChild, key)
            else:
                return_node = BSTFind()
                return_node.Node = node
                return_node.ToLeft = True
                return return_node

    def AddKeyValue(self, key, val):
        # добавляем ключ-значение в дерево
        found_node = self.FindNodeByKey(key)
        if found_node.NodeHasKey:
            return False
        else:
            new_node = BSTNode(key, val, found_node.Node)
            if found_node.Node is not None:
                if found_node.ToLeft:
                    found_node.Node.LeftChild = new_node
                else:
                    found_node.Node.RightChild = new_node
            else:
                self.Root = new_node
            self.Number += 1
            return True

    def FinMinMax(self, FromNode, FindMax):
        # ищем максимальный/минимальный ключ в поддереве
        # возвращается объект типа BSTNode
        return self.min_max_point(FromNode, FindMax)

    def min_max_point(self, node, max):
        if node is None:
            result_node = BSTFind()
            return result_node
        if max:
            if node.RightChild is not None:
                return self.min_max_point(node.RightChild, max)
        else:
            if node.LeftChild is not None:
                return self.min_max_point(node.LeftChild, max)
        result_node = BSTFind()
        result_node.Node = node
        return result_node


    def DeleteNodeByKey(self, key):
        found_node = self.FindNodeByKey(key)
        if found_node.NodeHasKey is None or found_node.Node is None:
            return False # если узел не найден
        else:
            if found_node.Node.LeftChild is not None and found_node.Node.RightChild is not None:
                if found_node.Node.RightChild.LeftChild is not None:
                    min_node = self.FinMinMax(found_node.Node.RightChild, False)
                    if min_node.Node.RightChild is not None:
                        min_node.Node.Parent.LeftChild = min_node.Node.RightChild
                        min_node.Node.RightChild.Parent = min_node.Node.Parent
                    else:
                        min_node.Node.Parent.LeftChild = None
                    min_node.Node.LeftChild = found_node.Node.LeftChild
                    found_node.Node.LeftChild.Parent = min_node.Node
                    min_node.Node.RightChild = found_node.Node.RightChild
                    found_node.Node.RightChild.Parent = min_node.Node
                    if found_node.Node.Parent is not None:
                        if found_node.Node.Parent.LeftChild == found_node.Node:
                            found_node.Node.Parent.LeftChild = min_node.Node
                        else:
                            found_node.Node.Parent.RightChild = min_node.Node
                    min_node.Node.Parent = found_node.Node.Parent
                else:
                    found_node.Node.RightChild.LeftChild = found_node.Node.LeftChild
                    found_node.Node.LeftChild.Parent = found_node.Node.RightChild
                    if found_node.Node.Parent is not None:
                        if found_node.Node.Parent.LeftChild == found_node.Node:
                            found_node.Node.Parent.LeftChild = found_node.Node.RightChild
                        else:
                            found_node.Node.Parent.RightChild = found_node.Node.RightChild
                    found_node.Node.RightChild.Parent = found_node.Node.Parent
            elif found_node.Node.LeftChild is None and found_node.Node.RightChild is None:
                if found_node.Node.Parent is not None:
                    if found_node.Node.Parent.LeftChild == found_node.Node:
                        found_node.Node.Parent.LeftChild = None
                    else:
                        found_node.Node.Parent.RightChild = None
                found_node.Node = None
            elif found_node.Node.LeftChild is not None:
                if found_node.Node.Parent is not None:
                    if found_node.Node.Parent.LeftChild == found_node.Node:
                        found_node.Node.Parent.LeftChild = found_node.Node.LeftChild
                    else:
                        found_node.Node.Parent.RightChild = found_node.Node.LeftChild
                    found_node.Node.LeftChild.Parent = found_node.Node.Parent
                else:
                    self.Root = found_node.Node.LeftChild
                    found_node.Node.LeftChild.Parent = None
            else:
                if found_node.Node.Parent is not None:
                    if found_node.Node.Parent.LeftChild == found_node.Node:
                        found_node.Node.Parent.LeftChild = found_node.Node.RightChild
                    else:
                        found_node.Node.Parent.RightChild = found_node.Node.RightChild
                    found_node.Node.RightChild.Parent = found_node.Node.Parent
                else:
                    self.Root = found_node.Node.RightChild
                    found_node.Node.RightChild.Parent = None
        self.Number -= 1
        return True
        # удаляем узел по ключу

    def Count(self):
        return self.Number  # количество узлов в дереве