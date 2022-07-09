// 34.1
let rec upto (n:int) =
    let mutable result = [n]
    let mutable i = n - 1
    while i > 0 do
        result <- i :: result
        i <- i - 1
    result

// 34.2
let rec dnto (n:int) =
    let mutable result = [1]
    for i in 2 .. n do
        result <- i :: result
    result

// 34.3
let rec evenn (n:int) =
    let mutable result = [(n-1)*2]
    let mutable i = 2*(n - 2)
    while i >= 0 do
        result <- i :: result
        i <- i - 2
    result