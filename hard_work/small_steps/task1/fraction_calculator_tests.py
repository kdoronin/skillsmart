import unittest

from fraction_calculator import FractionCalculator

class TestFractionCalculator(unittest.TestCase):
    def setUp(self):
        self.calculator = FractionCalculator()

    def test_add(self):
        self.assertEqual(self.calculator.add('1/4', '2/4'), '3/4')
        self.assertEqual(self.calculator.add('1/4', '1/4'), '1/2')
        self.assertEqual(self.calculator.add('1/4', '1/2'), '3/4')
        self.assertEqual(self.calculator.add('1/4', '1/3'), '7/12')
        self.assertEqual(self.calculator.add('-1/4', '1/4'), '0')
        self.assertEqual(self.calculator.add('-3/5', '4/7'), '-1/35')
        self.assertEqual(self.calculator.add('-1/2', '2/4'), '0')

    def test_subtract(self):
        self.assertEqual(self.calculator.subtract('1/4', '2/4'), '-1/4')
        self.assertEqual(self.calculator.subtract('1/4', '1/4'), '0')
        self.assertEqual(self.calculator.subtract('1/4', '1/2'), '-1/4')
        self.assertEqual(self.calculator.subtract('1/4', '1/3'), '-1/12')
        self.assertEqual(self.calculator.subtract('-1/4', '1/4'), '-1/2')
        self.assertEqual(self.calculator.subtract('-3/5', '4/7'), '-41/35')
        self.assertEqual(self.calculator.subtract('-1/2', '2/4'), '-1')
