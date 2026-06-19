# Prompt

Estou desenvolvendo uma API para um aplicativo mobile de controle de manutenção para motoristas de aplicativo.

A API está sendo desenvolvida em Laravel 12 e será consumida por um aplicativo React Native utilizando Expo.

Preciso implementar um sistema completo de autenticação utilizando Laravel Sanctum para autenticação via Token Bearer.

## Requisitos

### Instalar e configurar Laravel Sanctum

Realize toda a configuração necessária do Sanctum para uso em API.

---

## Endpoints

### Cadastro

```http
POST /api/register
```

Campos:

```json
{
  "name": "Jackson Ailva",
  "email": "jackson@gmail.com",
  "password": "12345678",
  "password_confirmation": "12345678"
}
```

Regras:

* Nome obrigatório
* Email obrigatório e único
* Senha mínima de 8 caracteres
* Confirmação de senha obrigatória

Retorno:

```json
{
  "user": {},
  "token": "TOKEN"
}
```

---

### Login

```http
POST /api/login
```

Campos:

```json
{
  "email": "joao@email.com",
  "password": "12345678"
}
```

Regras:

* Validar credenciais
* Gerar token Sanctum

Retorno:

```json
{
  "user": {},
  "token": "TOKEN"
}
```

---

### Usuário autenticado

```http
GET /api/me
```

Middleware:

```php
auth:sanctum
```

Retornar:

```json
{
  "id": 1,
  "name": "João Silva",
  "email": "joao@email.com"
}
```

---

### Logout

```http
POST /api/logout
```

Middleware:

```php
auth:sanctum
```

Ao realizar logout:

* Revogar apenas o token atual

Retorno:

```json
{
  "message": "Logout realizado com sucesso."
}
```

---

## Estrutura desejada

Criar:

### Controller

```text
app/Http/Controllers/Api/AuthController.php
```

Métodos:

* register()
* login()
* me()
* logout()

---

### Form Requests

Criar:

```text
app/Http/Requests/Auth/RegisterRequest.php
```

```text
app/Http/Requests/Auth/LoginRequest.php
```

Utilizar validação centralizada.

---

### Rotas

Adicionar em:

```php
routes/api.php
```

Rotas públicas:

```php
POST /register
POST /login
```

Rotas protegidas:

```php
GET /me
POST /logout
```

---

## Model User

Verificar e configurar:

```php
use Laravel\Sanctum\HasApiTokens;
```

Adicionar:

```php
HasApiTokens
```

na model User.

---

## Respostas da API

Padronizar todas as respostas JSON.

Exemplo de sucesso:

```json
{
  "success": true,
  "message": "Login realizado com sucesso.",
  "data": {}
}
```

Exemplo de erro:

```json
{
  "success": false,
  "message": "Credenciais inválidas."
}
```

---

## Tratamento de exceções

Implementar tratamento adequado para:

* Usuário não encontrado
* Senha inválida
* Email já cadastrado
* Token inválido
* Validação

---

## Segurança

Implementar:

* Hash de senha utilizando Hash::make()
* Tokens Sanctum
* Revogação de token atual no logout
* Validação via Form Request
* Não expor dados sensíveis

---

## Testes

Criar testes Feature completos para:

### RegisterTest

* cadastro com sucesso
* email duplicado
* senha inválida

### LoginTest

* login válido
* login inválido

### LogoutTest

* logout autenticado
* acesso sem autenticação

### MeTest

* usuário autenticado
* usuário não autenticado

Utilizar Pest PHP.

---

## Resultado esperado

Gerar o código completo de:

* Instalação Sanctum
* Configuração Sanctum
* AuthController
* RegisterRequest
* LoginRequest
* Rotas API
* Testes Pest
* Exemplos de requisições Postman
* Comentários explicando cada parte do código

Seguir princípios SOLID, Clean Code e padrões recomendados para APIs Laravel 12 consumidas por aplicativos mobile React Native.
