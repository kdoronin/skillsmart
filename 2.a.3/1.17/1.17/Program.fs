// 17.1
let rec pow = function
| (s,n) when n < 1 -> ""
| (s,1) -> s
| (s,n) -> s + pow(s, n-1)

// 17.2
let rec isIthChar = function
| (s : string,n : int,c : char) when n < 0 || n >= String.length s -> false
| (s : string,n : int,c : char) when s.[n] = c -> true
| (s : string,n : int,c : char) -> false

// 17.3
let rec occFromIth_rec = function
| (s: string, n : int, c : char, i : int) when n >= String.length s -> i
| (s: string, n : int, c : char, i : int) when n < 0 -> i
| (s: string, n : int, c : char, i : int) when s.[n] = c -> occFromIth_rec(s, n+1, c, i+1)
| (s: string, n : int, c : char, i : int) -> occFromIth_rec(s, n+1, c, i)


let rec occFromIth = function
| (s : string,n : int,c : char) -> occFromIth_rec(s,n,c,0)