### 不依赖 func_get_args()， 使用 ... 运算符 来实现 变长参数函数。
    function test(...$args){
        return $args;
    }
    var_dump(test(1,2,3)); // [1,2,3]
    var_dump(test(1)); // [1]
        
### 使用 ... 运算符进行参数展开
    function add($a, $b, $c) {
        return $a + $b + $c;
    }
    
    $operators = [2, 3];
    echo add(1, ...$operators); // 输出6
    
### 关于三元运算符 ?? ?:的一些特殊写法
    $a ?? 0 等同于 isset($a) ? $a : 0。
    $a ?: 0 等同于 $a ? $a : 0。
    empty: 判断一个变量是否为空(null、false、00、0、’0′、』这类，都会返回true)。
    isset: 判断一个变量是否设置(值为false、00、0、’0′、』这类，也会返回true)。