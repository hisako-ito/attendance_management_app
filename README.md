# 勤怠管理アプリ

## 環境構築

### Dockerビルド
1. ```git clone git@github.com:hisako-ito/attendance_management_app.git```
2. DockerDesktopアプリを立ち上げる
3. プロジェクト直下で、以下のコマンドを実行する

```
make init
```

※Makefileは実行するコマンドを省略することができる便利な設定ファイルです。コマンドの入力を効率的に行えるようになります。

＊ MacのM1・M2チップのPCの場合、no matching manifest for linux/arm64/v8 in the manifest list entriesのメッセージが表示されビルドができない場合があります。 エラーが発生する場合は、docker-compose.ymlファイルの「mysql」内に「platform」の項目を追加で記載してください

```
mysql:
    platform: linux/x86_64(この文追加)`
    image: mysql:8.0.26
    environment:
```

### メール認証
mailtrapというツールを使用しています。
以下のリンクから会員登録をしてください。  
([https://mailtrap.io/](https://mailtrap.io/))  
メールボックスのIntegrationsから 「laravel 7.x and 8.x」を選択し、  
.envファイルのMAIL_MAILERからMAIL_ENCRYPTIONまでの項目をコピー＆ペーストしてください。  
MAIL_FROM_ADDRESSは任意のメールアドレスを入力してください。
> [!NOTE]
> ※既に登録済みのメールアドレスでは会員登録できません。

### テストアカウント
---

#### 一般ユーザーアカウント
**name:** 一般ユーザ1    
**email:** [general1@gmail.com](mailto:general1@gmail.com)  
**password:** password  

**name:** 一般ユーザ2    
**email:** [general2@gmail.com](mailto:general2@gmail.com)  
**password:** password  

---

#### 管理者アカウント
**name:** 管理者ユーザ1      
**email:** [admin1@gmail.com](mailto:admin1@gmail.com)  
**password:** password  

**name:** 管理者ユーザ2    
**email:** [admin2@gmail.com](mailto:admin2@gmail.com)  
**password:** password  

---
### PHPUnitを利用したテストに関して
以下のコマンド:  
    
```
docker-compose exec php bash
```
```
php artisan migrate:fresh --env=testing
```
```
./vendor/bin/phpunit
```
    

## 使用技術(実行環境)
* PHP 7.4.9
* Laravel 8.83.29
* MySQL 15.1

## ER図
![attendance_management](https://github.com/user-attachments/assets/010af4d1-cdc3-4979-ab69-78cbdd1ec914)

## URL
* 開発環境： [http://localhost/login](http://localhost/login)(一般ユーザーログイン画面)  
           　　　 　　[http://localhost/admin/login](http://localhost/admin/login)(管理者ログイン画面) 
* phpMyAdmin： [http://localhost:8080/](http://localhost:8080/)

