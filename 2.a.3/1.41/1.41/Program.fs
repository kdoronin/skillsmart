// 41.4.1
let list_filter f xs =
    List.foldBack (fun x y -> if f x = false then y else x::y) xs []

// 41.4.2
let sum (p, xs) = List.foldBack (fun x y -> if p x = false then y else x + y) xs 0

let revhelper = fun head tail -> List.fold(fun headin tailin -> tailin::headin) [] tail :: List.fold(fun headin tailin -> tailin::headin) [] head

// 41.4.3
let revrev lst = List.fold revhelper [] lst