class BSTNode:

    def __init__(self, key, parent):
        self.NodeKey = key  # ключ узла
        self.Parent = parent  # родитель или None для корня
        self.LeftChild = None  # левый потомок
        self.RightChild = None  # правый потомок
        self.Level = 0  # уровень узла


class BalancedBST:

    def __init__(self):
        self.Root = None  # корень дерева

    def GenerateTree(self, a):
        a.sort()
        return self.one_step_generator(a, None, 0, len(a) - 1, 0)
    # создаём дерево с нуля из неотсортированного массива a
    # ...

    def one_step_generator(self, a, parent, start_a, end_a, parent_lvl):
        if end_a - start_a > 0:
            center_a = start_a + ((end_a - start_a) // 2)
            node = BSTNode(a[center_a], parent)
            if parent is None:
                node.Level = parent_lvl
                self.Root = node
            else:
                node.Level = parent_lvl + 1
                if a[center_a] < parent.NodeKey:
                    parent.LeftChild = node
                else:
                    parent.RightChild = node
            self.one_step_generator(a, node, start_a, center_a - 1, node.Level)
            self.one_step_generator(a, node, center_a + 1, end_a, node.Level)
            return node
        elif end_a - start_a == 0:
            node = BSTNode(a[start_a], parent)
            node.Level = parent_lvl + 1
            if a[start_a] < parent.NodeKey:
                parent.LeftChild = node
            else:
                parent.RightChild = node
            return node


    def IsBalanced(self, root_node):
        if root_node is None:
            return True
        else:
            leftcount = self.leaves_count(root_node.LeftChild, 0)
            rightcount = self.leaves_count(root_node.RightChild, 0)
            leftlvl = self.max_lvl(root_node.LeftChild, root_node.Level)
            rightlvl = self.max_lvl(root_node.RightChild, root_node.Level)
            count = (leftcount > rightcount and leftcount - rightcount < 2) or (rightcount > leftcount and rightcount - leftcount < 2) or (rightcount == leftcount)
            lvl = (leftlvl > rightlvl and leftlvl - rightlvl < 2) or (rightlvl > leftlvl and rightlvl - leftlvl < 2) or (rightlvl == leftlvl)
            return count and lvl  # сбалансировано ли дерево с корнем root_node


    def leaves_count(self, node, sum):
        if node is None:
            return sum
        else:
            sum += 1
            if node.LeftChild is not None:
                sum = self.leaves_count(node.LeftChild, sum)
            if node.RightChild is not None:
                sum = self.leaves_count(node.RightChild, sum)
            return sum

    def max_lvl(self, node, lvl):
        if node is None:
            return lvl
        else:
            if node.Level > lvl:
                lvl = node.Level
            leftlvl = lvl
            rightlvl = lvl
            if node.LeftChild is not None:
                leftlvl = self.max_lvl(node.LeftChild, lvl)
            if node.RightChild is not None:
                rightlvl = self.max_lvl(node.RightChild, lvl)
            return leftlvl if leftlvl >= rightlvl else rightlvl