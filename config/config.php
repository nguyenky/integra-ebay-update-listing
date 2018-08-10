<?php

//* PRODUCTION ////////////////////////////////////////////

define('NEW_EBAY_HOST', 'https://api.ebay.com/');

define('DEV_ID', '24b32b7f-f7a9-4e99-bfce-b7a84113f5b6');
define('APP_ID', 'KBCwareT-fa69-4a39-a049-60ca79a5fff2');
define('CERT_ID', '69128ac4-71d1-475d-ba77-92a6729ad69c');
define('SITE_ID', '100');
define('EBAY_SELLER', 'qeautoparts1');
define('EBAY_TOKEN', 'AgAAAA**AQAAAA**aAAAAA**fsNuWA**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wCloGgCpaEqQidj6x9nY+seQ**NosBAA**AAMAAA**7+MiDKWT7B1VlFXExufh4Eedx5WzkgWlmTjSN5YaxKXihyfE0dUkla6bpc47+xZH5YzR+E04ZCyinwnCH4iOYXDpgZMnovv34x13OzalirA7MMwwjlx9qT2+3l0m42I9t6j+ZdEhWURMA/47/kbgt5k6baA5cXn4Syy7kiDyzdjLORubXP49K9ip59kJIZ5b8J4DnWslV8fSkkR2DkfiZ6+WlvLBxxv1KuVB59TZvbOARoRugOZAgG0iJHc+2faJhtHVt0m9JR+TmOvoMT6a5y9Mf2W4+shmeK5ena63rZ4p7aeMY0YLYHy1xVkphWTmI8j5qRJEsYIVmOLMCAedYbMKejFKHZOJqNjLtoBd4egz4P0mZkveGmF6PeYAvr/w+V+1fSPpdnU6KMUhiNGTrtWgi/dRBdylC+XhS/OwvbMdYtZXg1eaIbR6eVANcRlQuW93+Wm5YNtqUIIfD5ej6JEoVk8EOMijzq/DUT2Q1Op8sLNvpZWMvSiOeP+ccpxsWdh9aIzRq73N3X8Z3o4T7uudbnaDzeu3QZqzCYWUf0r+wK1uab8WP36NcsvAEafJyacnrpdgRvPLLkFv5A+CQWtb9HddciIaXoCsKMv6RwnrxQrvkdpndCYdgasljB355enjXJDnjP3fwAFMO3vCnbPsdJ44jk6g2oaRkcPsFOA36f4Ddbd9cPJRgdzveZhsXdEphMDmJA5fJ6TX68uFkT0964l6C1IXP+xRN02cioYcrEQlt/n5xwDY3junWBxU');
// Dont forget to update ServiceEndpointsAndTokens.php also!

define('EBAY_PAYMENT_PROFILE_ID', '93290711014');
define('EBAY_RETURN_PROFILE_ID', '93290710014');
define('EBAY_SHIPPING_PROFILE_ID', '102557276014');

define('AWS_ACCESS_KEY_ID', 'AKIAILMIF3RH75U6GSRQ');
define('AWS_SECRET_ACCESS_KEY', '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');  
define('APPLICATION_NAME', 'EOC');
define('APPLICATION_VERSION', '1.0');
define('MERCHANT_ID', 'A22LWEMCXESXCG');
define('MARKETPLACE_ID', 'ATVPDKIKX0DER');

//*////////////////////////////////////////////////////////

/* SANDBOX ///////////////////////////////////////////////

define('EBAY_HOST', 'https://api.sandbox.ebay.com/');

define('DEV_ID', '24b32b7f-f7a9-4e99-bfce-b7a84113f5b6');
define('APP_ID', 'KBCwareT-f1a7-44e7-b664-749ae6a4b9d9');
define('CERT_ID', 'b214f51e-871e-4f4c-ba4f-eb1ee763e401');

define('EBAY_TOKEN', 'AgAAAA**AQAAAA**aAAAAA**H10dUA**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wFk4GhCZGKogmdj6x9nY+seQ**jeEBAA**AAMAAA**0msZ5b7krxcOT2XHLQLhDIOMEbqSkT4lKs4vt0fHHBktisCQCtgv/+PqNTE/k/OAP0I/MJzhaUP9EJngbhqIGwevXr3kbJ0vgu7cfES9LnBi0EizwfH/sA2Sh1J2ua6VEiw2ZW1N/zvomt5f70kgWk8VD6HjJWz3a0cF8QZlmfw8mZxBarUYYyTDxqtu3PPj8I+R0MKcLFjx85h25UA8ybPhsHy/XjykG1nEGQL71E6GNv1jRedHxwuFJT8d3Rupr9uA3fCUW0BAiY+ROjHnbca+7DXHPb0ND0EbbQKhvhNx0ScC7iBIlT5QC+ntpgX9iQJr+wIFV4IeIW5IBxwIf/zoif0agVqK6y55dDd/63hkkdJbDKV+Qjl50oPNyYEyGPPzG5bls6UJfO4lTDuwWAU11Ekz1a6cLaPGSSvWivmcNqSNhusPfojHTlRg/eDwmgtwREyeXnE/S8uhbZqLdfiKNg73Khy50PhykNKTrpMSp82SP5eDxSqWznxU4Dt97OXVuDg5fl4rbjg6Cwq2Eks0oOQmIszTXPA8CkXSPGci5u72wNEqwz5ivlCqASGnRthiD/+J/Uf1vX21oPxrE8vEE+WA9W9BKzYpCszmhgxV8WRs1/VUwwr0MP86cJoHDUj1u/yoNu7sE5toXpissrya+s29z+kznbobawFiC4sg7AjqmLCTCXMExl40xcrLo73XXWfP/NU7vVajJhIZarvS9ao/cBP+vxHaLEyTxHgOyjkWTCl23p3yHz1IgAhK');

//*////////////////////////////////////////////////////////

?>
