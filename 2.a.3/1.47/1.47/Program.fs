// 47.4.1
let f n =
    let mutable i = n
    let mutable result = 1
    while i > 1 do
        result <- result*i
        i <- i - 1
    result
        
        

// 47.4.2
let fibo n =
    let mutable prev = 1
    let mutable middle = 1
    let mutable result = 0
    let mutable i = 0
    while i < n do
        middle <- result
        result <- result + prev
        prev <- middle
        i <- i + 1
    result