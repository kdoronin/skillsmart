let try_find key m =
    let mutable result = None
    if Map.containsKey key m then
        result <- Some(Map.find key m)
    result