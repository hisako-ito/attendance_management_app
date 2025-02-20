# 勤怠管理アプリ

## 環境構築

### Dockerビルド
1. `git clone git@github.com:hisako-ito/attendance_management_app.git`
2. DockerDesktopアプリを立ち上げる
3. `docker-compose up -d --build`

＊ MacのM1・M2チップのPCの場合、no matching manifest for linux/arm64/v8 in the manifest list entriesのメッセージが表示されビルドができない場合があります。 エラーが発生する場合は、docker-compose.ymlファイルの「mysql」内に「platform」の項目を追加で記載してください

```
mysql:
    platform: linux/x86_64(この文追加)`
    image: mysql:8.0.26
    environment:
```

### Laravel環境構築
1. docker-compose exec php bash
2. composer install
3. 「.env.example」ファイルを 「.env」ファイルに命名を変更。  または、.envファイルを作成します。　　
4. env以下の環境変数を追加
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
5. アプリケーションキーの作成
```
php artisan key:generate
```
6. マイグレーションの実行
```
php artisan migrate
```
7. シーディングを実行する
```
php artisan db:seed
```

### メール認証
mailtrapというツールを使用しています。
以下のリンクから会員登録をしてください。
([https://mailtrap.io/](https://mailtrap.io/))
メールボックスのIntegrationsから 「laravel 7.x and 8.x」を選択し、　
.envファイルのMAIL_MAILERからMAIL_ENCRYPTIONまでの項目をコピー＆ペーストしてください。
MAIL_FROM_ADDRESSは任意のメールアドレスを入力してください。

### テストアカウント
name: 一般ユーザ1
email: general1@gmail.com
password: password
name: 一般ユーザ2
email: general2@gmail.com
password: password

name: 管理者ユーザ1
email: admin1@gmail.com
password: password
name: 管理者ユーザ2
email: admin2@gmail.com
password: password

## 使用技術(実行環境)
* PHP 7.4.9
* Laravel 8.83.8
* MySQL 15.1

## ER図



## URL
* 開発環境： [http://localhost](http://localhost)
* phpMyAdmin： [http://localhost:8080/](http://localhost:8080/)
* mailtrap： 

