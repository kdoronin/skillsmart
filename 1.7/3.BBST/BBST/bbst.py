def GenerateBBSTArray(a):
    a.sort()
    if len(a) == 0:
        return a
    else:
        b = [None] * len(a)
        return one_step_generator(a, b, 0, len(a) - 1, 0)


def one_step_generator(a, b, start_a, end_a, num_b):
    if end_a - start_a > 0 and num_b < len(a):
        center_a = start_a + ((end_a - start_a) // 2)
        b[num_b] = a[center_a]
        b = one_step_generator(a, b, start_a, center_a - 1, 2 * num_b + 1)
        b = one_step_generator(a, b, center_a + 1, end_a, 2 * num_b + 2)
    elif end_a == start_a and num_b < len(a):
        b[num_b] = a[start_a]
    return b
