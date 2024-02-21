import unittest
from BBST.bbst import GenerateBBSTArray

class testGenerateBBSTArray(unittest.TestCase):
    def setUp(self):
        self.first_list = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15]
        self.second_list = [3, 2, 1]
        self.second_control_list = [2, 1, 3]
        self.third_list = [8, 4, 12, 2, 6, 10, 14, 1, 3, 5, 7, 9, 11, 13, 15]
        self.four_list = []
    def test_generate_bbst_array(self):
        first_generated = GenerateBBSTArray(self.first_list)
        self.assertEqual(first_generated, self.third_list)
        second_generated = GenerateBBSTArray(self.second_list)
        self.assertEqual(second_generated, self.second_control_list)
