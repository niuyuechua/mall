<?php
return [	
		//应用ID,您的APPID。
		'app_id' => "2016092500596049",

		//商户UID
		'seller_id'=>'2088102177262383',

		//商户私钥
		'merchant_private_key' => "MIIEpQIBAAKCAQEArPsjzFiaQbMdBlYrD6jOdIIiRkPK6vC2csuVVfcbvLGYwGmUk74AlLdF/ZqT1zYBjdBF0M2k4ockFiIRh8b3XXAGruQLuJBNiMX3joI0iJgyX9yMpuvFwqxzVKM6uC4Ws5mNyzAT99UwOxTDcCKqBquFYZ9H1PzSdXJkRoBkdX0T10Y0I+PFJGWH0CsRto5ZkBKJZJVIln/zPS2T0rn8Ggqlclg6IMNp64jF5C+kFeqKRG5WwISe3UYtA7/qZgx2qKvr0dVOAK8cdg3zsYfaUmv0xNVR7w3e59Vcn8AqAQERYo3xcrpgriKuyXkqjtoOnPm8xLVv5paSlqt8hEAOCwIDAQABAoIBAB6Fwe9QwwdMv5ZapgaBVsygGcQkOi3yg38GBTfB/pbxD28EMj9Pi3KyVBtHp0aWNPf9BBSv9KQ0DF4LbOR0azmFhuhdPOQ4MjYsGF6BAxwHHvxjQj5B5AdRvpf0pWvSVhcixS5RMXTNnVEPfNzQgQfkRAjRvi8K3Wfz41W9WfyQ5vQhKTEzWYmY4rPjjiAg5fLQNRjEvhkhWUUKcSneBUHqk31XJ4HNt1/FM6MVdFjw0S9eL++8KBZa0QFitYivfbepsopMZErFJVeyQv4yLbkcUsNvZ7CNewNuBgMAE1145E4EB+NG9TugT1dxNcGmF/A6M9PdbQ8fEJvZkIJj7wECgYEA4ojDbqFsYMylsPfcfPeQYDkEupSwjOQUMSFF3qSEYq0xfDVNZVed8V2tP7QuedJqEBen+iY1duizTbb+5LZHmNOHpdnM37mb7aWeXbrYq7R6PG28TWkEw202q0OVgpxq5Nc/K1E5ySs5k1xfcerfnNT+BtUbNwB2DO3ZIT1Fq6sCgYEAw3sjSN3Ws08bN4Zvp4abXwCrV2yWZBQxcFLmxf+PrLsKzpB8sEgZ2D6Xh8ZtkE9A2c43dNK2w6kQc4d4Kz7YHgzxCtEuze5QuRyZJSPRt/mMgmsP6pRELiSdPny1AZd/YIwvj91YBjsmft/C6kESDWbiYkxgM3w3unWuCvZ5xyECgYEAvx45G03u/J/LLkl+6KOIV9XjCnLFnKgT7PswUk0kL4gE1tC1ckmARBEPSE6AY2DFALykiCPSOXbLR0abN2QddW8I40CkWx/h0JZIzLUFdZ92/SJrmjd+wE2UsNr8+Utz14tNjQMjKHhiQ9PL5nUMoOkFQ8hpBHdMIU5NQIDMFa8CgYEAmEB8RvChnJ7sb97BcBWjRedbjIgCyof/yaCIJYba/Inh7OkUdKhzmL2HgDTIeTGBLSM0hkToHJS9P34v1l7oLN+fjfFHJxWKweLIVOkFGum+yoVgrDA2uknCPz0aLdc1WplVIhQfzxqa7Q6S3ak4yj9r62vZMcB6Fzw5BkjaqIECgYEAq/M5AMwOhNrag0DbPWqhY4K+ger9dSYNzo6bCx1JMsWeXP4fL/RR5UoK60zK88ihcYDgYPEPnUYoYd8KjDu2Wa8pQSMjvmgaOtOzkvbh0E5A7HZ5jCGnZNhSe9TD187PQsWfJaPQ06+Jh8gke6YKXkRR7shDSgtWv/8gqX9Z0N0=",
		
		//异步通知地址
		'notify_url' => "http://39.107.78.144/cart/returnPay",
		
		//同步跳转
		'return_url' => "http://www.mall.com/cart/paySucc",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwaRFcaf9e4oVDd789mLqHmncnpHyVc1/yIrkHFslQpqK32tPvEtLo4yqzAqOyTUvNh4Rlw/ZyvcD8i6TOwb8njsuSlPwKSwXn3OtIO637Us5WDCZT1ntJypH8oZZsFMMt0klwrj+9/dhpkV4QdTuCl7e5o6oT5QaoSdlQDoDfwiwMDmXxC6aAe4bsmKdVFSCTV742+s7MKZAoQFPfX6bhypEI4SpRS5ZGzulyDGEM7uZDnpL3aDbfkSJlkhQoC4yqomONhuRlE8GncT+52FhvT5Xm+7DPeHx3z4Fcmu8pJFazrX50Nz0KC0XbepbDhO4vSEuAx/Y6waaTeC3aaHqlQIDAQAB",
    ];