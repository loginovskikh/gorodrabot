SELECT shop.article, shop.dealer, shop.price from shop
    INNER JOIN shop shop1 ON shop1.article = shop.article
group by shop.article, shop.price, shop.dealer
HAVING shop1.price = MAX(shop.price)
ORDER BY shop1.article;