//// 20.3.1
let vat n (x: float) =
    x + x / float(100) * float(n)

// 20.3.2
let unvat n (x: float) =
    float(x) - float(x) / (float(100) + float(n)) * float(n)

// 20.3.3
let rec min f =
    let mutable i = 0
    while f i <> 0 do
        i <- i + 1
    i