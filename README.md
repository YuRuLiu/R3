# R3

api

url - https://lab1-betsy-liu.c9users.io/R3/api名稱.php?參數=值

ex. request - https://lab1-betsy-liu.c9users.io/R3/getBalance.php?username=betsy

reponse - {"result":"true","username":"betsy","balance":"300"}

1.新增帳號

> api名稱 - addUser

> 參數1 - username(帳號)

2.取得餘額

> api名稱 - getBalance

> 參數1 - (string)username(帳號)

3.轉帳

> api名稱 - transfer

> 參數1 - (string)username(帳號)

> 參數2 - (int)transid(轉帳序號)

> 參數3 - (string)type(轉帳型態) (IN,OUT)

> 參數4 - (int)amount(轉帳金額)

4.轉帳確認

> api名稱 - checkTransfer

> 參數1 - (string)username(帳號)

> 參數2 - (int)transid(轉帳序號)