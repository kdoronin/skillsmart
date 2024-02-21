// 49.5.1
let even_seq = Seq.initInfinite (fun i -> 2*(i+1))


let rec factorial = function 
| 1  -> 1
| n  -> n * factorial(n - 1)

// 49.5.2
let fac_seq = Seq.initInfinite (fun i -> factorial i)

// 49.5.3
let seq_seq = Seq.initInfinite (fun i ->
    match i with
        | 0 -> 0
        | i when i%2 = 1 -> (-i/2) - 1
        | i -> i/2)