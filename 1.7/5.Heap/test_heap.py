import unittest
from Heap.heap import Heap


class testHeap(unittest.TestCase):
    def setUp(self):
        self.array1 = [11, 9, 4, 7, 8, 3, 1, 2, 5, 6]
        self.first_heap = Heap()
        self.first_heap.MakeHeap(self.array1, 3)
        self.array2 = [1, 2, 3]
        self.second_heap = Heap()
        self.second_heap.MakeHeap(self.array2, 1)
        self.array3 = [11, 9, 8, 7, 6, 5, 4, 3, 2, 1]
        self.third_heap = Heap()
        self.third_heap.MakeHeap(self.array3, 3)
        self.array4 = [1]
        self.four_heap = Heap()
        self.four_heap.MakeHeap(self.array4, 0)


    def test_make_heap(self):
        self.assertEqual(self.first_heap.HeapArray[0], 11)
        self.assertEqual(self.first_heap.HeapArray[1], 9)
        self.assertEqual(self.first_heap.HeapArray[2], 4)
        self.assertEqual(self.first_heap.HeapArray[3], 7)
        self.assertEqual(self.first_heap.HeapArray[4], 8)
        self.assertEqual(self.first_heap.HeapArray[5], 3)
        self.assertEqual(self.first_heap.HeapArray[6], 1)
        self.assertEqual(self.first_heap.size, 15)
        self.second_correct_array = [3, 1, 2]
        self.assertEqual(self.second_heap.HeapArray[0], 3)
        self.assertEqual(self.second_heap.HeapArray[1], 1)
        self.assertEqual(self.second_heap.HeapArray[2], 2)
        self.assertEqual(self.second_heap.size, 3)
        self.assertEqual(self.third_heap.HeapArray[0], 11)
        self.assertEqual(self.third_heap.HeapArray[1], 9)
        self.assertEqual(self.third_heap.HeapArray[2], 8)
        self.assertEqual(self.third_heap.HeapArray[3], 7)
        self.assertEqual(self.third_heap.HeapArray[4], 6)
        self.assertEqual(self.third_heap.HeapArray[5], 5)


    def test_get_max(self):
        max_first = self.first_heap.GetMax()
        self.assertEqual(max_first, 11)
        correct_array = [9, 8, 4, 7, 6, 3, 1, 2, 5]
        p = 0
        for i in correct_array:
            self.assertEqual(self.first_heap.HeapArray[p], i)
            p += 1
        max_first_2 = self.first_heap.GetMax()
        self.assertEqual(max_first_2, 9)
        max_first_3 = self.first_heap.GetMax()
        self.assertEqual(max_first_3, 8)
        max_second = self.second_heap.GetMax()
        self.assertEqual(max_second, 3)
        correct_second_array = [2, 1]
        self.assertEqual(self.second_heap.HeapArray[0], correct_second_array[0])
        self.assertEqual(self.second_heap.HeapArray[1], correct_second_array[1])
        max_second2 = self.second_heap.GetMax()
        self.assertEqual(max_second2, 2)
        max_second3 = self.second_heap.GetMax()
        self.assertEqual(max_second3, 1)
        max_second4 = self.second_heap.GetMax()
        self.assertEqual(max_second4, -1)
        max_four = self.four_heap.GetMax()
        self.assertEqual(max_four, 1)


    def test_add(self):
        self.first_heap.Add(15)
        self.array1_correct = [15, 11, 4, 7, 9, 3, 1, 2, 5, 6, 8]
        p = 0
        for i in self.array1_correct:
            self.assertEqual(self.first_heap.HeapArray[p], i)
            p += 1
        self.first_heap.Add(12)
        self.array_next_correct = [15, 11, 12, 7, 9, 4, 1, 2, 5, 6, 8, 3]
        p = 0
        for i in self.array_next_correct:
            self.assertEqual(self.first_heap.HeapArray[p], i)
            p += 1