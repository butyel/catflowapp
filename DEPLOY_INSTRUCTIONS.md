# Instalação e Configuração do CATFLOW

O CATFLOW é um SaaS construído em PHP (vanilla, PDO), MySQL e com marcação TailwindCSS via CDN com foco em ser uma Progressive Web App (PWA) instalável diretamente dos navegadores.

## Requisitos do Servidor

- Servidor Web (Apache, Nginx, LiteSpeed)
- PHP 7.4 ou superior (Recomendado 8.x)
- Extensão `pdo_mysql` habilitada no PHP
- Banco de Dados MySQL 5.7+ ou MariaDB 10.3+
- HTTS habilitado (SSL) - **OBRIGATÓRIO PARA O FUNCIONAMENTO COMO PWA.**

## Passo a Passo para Deploy

### 1. Preparação dos Arquivos
Suba toda a pasta do projeto para o diretório público do seu servidor (ex: `/var/www/html/` no apache, ou `public_html` numa hospedagem cPanel/Hostinger).

Assegure-se de que a pasta `assets/uploads/` tem permissões de escrita (`chmod 777 assets/uploads` ou `755` através de propriedade chown do apache/nginx dependendo do ambiente) para que as fotos dos gatos possam ser salvas.

### 2. Configurando o Banco de Dados

1. Acesse seu painel de banco de dados (ex. PHPMyAdmin, DBeaver)
2. Crie um banco de dados novo com collation `utf8mb4_unicode_ci` (ex: `catflow`).
3. Importe o arquivo `database.sql` incluído na raiz do projeto para criar a estrutura das tabelas.

### 3. Conectando a Aplicação ao Banco de Dados

1. Abra o arquivo `src/db.php`.
2. Edite as variáveis de conexão com as suas credenciais reais de produção:
   ```php
   $host = 'localhost'; // normalmente localhost em hospedagens padrão
   $db   = 'nome_do_seu_banco';
   $user = 'usuario_do_banco';
   $pass = 'senha_super_segura';
   ```

### 4. Criação do primeiro Administrador
O sistema permite registro aberto para tutores e ongs. Para se tornar `admin`:
1. Cadastre-se normalmente na tela inicial (`register.php`).
2. Vá no banco de dados, tabela `users`, ache o seu usuário e altere a coluna `role` para `admin`.

### 5. Ativação do PWA e Teste no Celular
O projeto já conta com o `manifest.json` e o `service-worker.js`. 
1. Por exigências modernas do Service Worker e políticas de instalação do PWA, o site **precisa ser servido via HTTPS**. (A menos que esteja num `localhost` de teste).
2. Abra o site no navegador Chrome (Android) ou Safari (iOS).
3. O navegador irá oferecer no menu de opções "Adicionar à Tela Inicial" (Add to Home Screen). 
4. Ao clicar, o CATFLOW será instalado ganhando aparência de App nativo de tela cheia sem barra de endereço.

## Expansão Futura (SaaS)
O sistema foi arquitetado desde o primeiro dia separando dados do usuário via chave estrangeira `user_id` em praticamente todas as tabelas (gatos e financeiro, enquanto as filhas vinculam ao gato).
Desta forma o `id` da sessão governa quem vê o que, garantindo o multi-tenancy fundamental para o modelo SaaS. Apenas contas promovidas para `role='admin'` têm acesso global de visualização/gerenciamento pela interface para fins de manutenção.

### 6. URLs Relativas e Subpastas
O sistema está programado inteiro usando caminhos relativos (ex: `api/login.php` em vez de `/api/login.php`). Isso significa que você pode hospedá-lo **em qualquer subpasta** (ex: `http://localhost/CATFLOW/`) sem que os links quebrem.
Contudo, atente-se que PWAs instalados em subpastas consideram apenas a raiz (`scope`) declarada no Service Worker. Para testes locais, prefira sempre criar um Virtual Host para que o sistema rode diretamente na raiz (ex: `http://catflow.test`).
