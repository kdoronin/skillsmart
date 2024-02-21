// 48.4.1
let fibo1 n n1 n2 =
    let rec fiborec p n1 n2 =
        if p = n then n1 + n2
        elif n = 0 then n2
        elif n = 1 then n1
        else fiborec (p+1) (n1+n2) n1
    fiborec 2 n1 n2

//48.4.2
let rec fibo2 n c =
    if n <= 2 then 1
    else fibo2 (n-1) (fun f ->  f + c(n-1)) + fibo2 (n-2) (fun f -> f + c(n-2))
     

//// 48.4.3
let bigList n k =
    let rec big_list_loop n p =
        if n = 0 then p
        else big_list_loop (n - 1) (1::p)
    big_list_loop n []