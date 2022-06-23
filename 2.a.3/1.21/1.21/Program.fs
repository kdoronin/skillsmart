let curry f : int =
    let g (x:int) : int =
        let h (y:int) : int = f(x, y)

//let uncurry f = ...
