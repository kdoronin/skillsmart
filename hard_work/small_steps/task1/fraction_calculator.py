## Калькулятор операций с дробями
## Разработайте функции для выполнения основных арифметических операций (сложение, вычитание, умножение, деление) с дробями.

class FractionCalculator:
    def add(self, a, b):
        result_numerator = self.get_numerator(a) * self.get_denominator(b) + self.get_numerator(b) * self.get_denominator(a)
        result_denominator = self.get_denominator(a) * self.get_denominator(b)
        return self.reduce_fraction(result_numerator, result_denominator)

    def get_numerator(self, fraction):
        return int(fraction.split('/')[0])

    def get_denominator(self, fraction):
        return int(fraction.split('/')[1])

    def reduce_fraction(self, numerator, denominator):
        gcd = self.gcd(numerator, denominator)
        if gcd == 0 or numerator == 0:
            return '0'
        if abs(denominator // gcd) == 1:
            return str(numerator // gcd)
        return str(numerator // gcd) + '/' + str(denominator // gcd)

    def gcd(self, a, b):
        while b:
            a, b = b, a % b
        return a

    def subtract(self, a, b):
        return self.add(a, '-' + b)

    def multiply(self, a, b):
        result_numerator = self.get_numerator(a) * self.get_numerator(b)
        result_denominator = self.get_denominator(a) * self.get_denominator(b)
        return self.reduce_fraction(result_numerator, result_denominator)

    def divide(self, a, b):
        return self.multiply(a, self.reverse_fraction(b))

    def reverse_fraction(self, fraction):
        return str(self.get_denominator(fraction)) + '/' + str(self.get_numerator(fraction))

