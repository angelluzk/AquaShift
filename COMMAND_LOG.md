# 📓 Diário de Comandos do Projeto AquaShift

Este documento é um log didático e 100% completo de todos os comandos de terminal usados para construir, configurar, depurar e rodar o projeto **AquaShift**. Ele é um "diário de bordo" que mostra não apenas os sucessos, mas também os erros e as correções cruciais que aplicamos.

## Legenda dos Terminais

* `PS C:\>` - Terminal **PowerShell** do Windows (para setup do WSL).
* `angel@Angel MINGW64 /c/Dev$` - Terminal **Git Bash** (usado no início, depois abandonado).
* `angel@Angel:/mnt/c/Dev/aquashift$` - Terminal **Ubuntu (WSL2)** (O terminal principal do projeto).
* `>>>` - Console interativo **Tinker** (acessado via `sail artisan tinker`).

---

## Fase 0: Setup do Ambiente (Windows, WSL2, Docker)

O objetivo desta fase era preparar a máquina Windows para rodar um ambiente de desenvolvimento Linux profissional com Docker.

### 1. PowerShell (Como Administrador)

```bash
# 1. Instala o WSL2 e a distribuição padrão (Ubuntu)
# Exige reinicialização do PC.
wsl --install
````

> **Por quê?** O Laravel Sail (Docker) precisa do WSL2 para rodar de forma performática no Windows.

```bash
# 2. [CORREÇÃO DE ERRO] Desliga o WSL2
# Usamos este comando posteriormente para reiniciar a integração com o Docker.
wsl --shutdown
```

> **Por quê?** Quando o Docker não conseguia autenticar (`error getting credentials`), reiniciar o WSL2 forçou uma reconexão limpa com o Docker Desktop.

### 2\. Docker Desktop (Manual)

  * Instalação manual do **Docker Desktop for Windows** (marcando a opção "Use WSL 2").
  * **Configuração Crítica (Manual):** Em `Settings > Resources > WSL Integration`, ativar a integração com a distribuição "Ubuntu".

> **Por quê?** Esta configuração é a "ponte" que permite o terminal Ubuntu (`sail`) controlar o Docker Desktop (Windows).

-----

## Fase 1: Setup Inicial e Tentativas (Git Bash)

Nossa primeira tentativa foi usar um setup local no Windows, o que gerou conflitos de dependência e nos levou a migrar para o Docker.

```bash
# 1. Criação do projeto (Nome errado, abandonado)
composer create-project laravel/laravel smartwash-scheduler

# 2. Criação do projeto (Nome correto)
composer create-project laravel/laravel aquashift
cd aquashift

# 3. Tentativa de instalação do Breeze (Sintaxe antiga, falhou)
php artisan breeze:install vue --inertia

# 4. Instalação correta do Breeze (Interativo)
php artisan breeze:install 
# (Respondemos: vue, inertia, etc.)

# 5. Instalação do NPM (Falhou)
npm install
```

> **Por quê?** O `npm` falhou com um erro `ERESOLVE`, pois o `package.json` pedia `vite@^7.0.0` mas o `@vitejs/plugin-vue` pedia `vite@^5.0.0 || ^6.0.0`.
> **Correção:** Editamos manualmente o `package.json` para `vite: "^5.0.0"`.

```bash
# 6. Nova tentativa de instalação do NPM (Funcionou)
npm install
npm run dev
```

> **Conclusão:** Neste ponto, decidimos que o setup local era muito frágil e migramos para o **Docker/Sail** para um ambiente profissional.

-----

## Fase 2: Migração para o Docker/Sail (Corrigindo Erros)

Esta foi a fase mais complexa, onde migramos para o Docker e depuramos o ambiente passo a passo.

### 1\. Limpeza (Git Bash)

```bash
# 1. Apaga o projeto local antigo
rm -rf aquashift
```

### 2\. Tentativa de Instalação (Git Bash)

```bash
# 2. Tentativa de criar com Sail (Sintaxe errada, falhou)
composer create-project laravel/laravel:^11.0 aquashift --with=pgsql
rm -rf aquashift # Limpa de novo

# 3. Criação correta (sem a flag)
composer create-project laravel/laravel:^11.0 aquashift
cd aquashift

# 4. Adiciona o Sail
composer require laravel/sail --dev

# 5. Tenta criar o docker-compose.yml (Falhou silenciosamente)
php artisan sail:install --with=pgsql
```

> **Por quê?** O `php artisan` rodando no Git Bash/Windows não conseguiu criar o `docker-compose.yml` (provavelmente por falta de extensões PHP locais).

```bash
# 6. [CORREÇÃO] Baixa o docker-compose.yml manualmente
curl -s [https://laravel.com/docker-compose.yml](https://laravel.com/docker-compose.yml) -o docker-compose.yml
```

> **Correção:** Editamos manualmente este arquivo para trocar `mysql` por `pgsql` e ajustamos o `.env`.

```bash
# 7. Tentativa de subir o Sail (Falhou)
sail up -d
```

> **Por quê?** O Git Bash (MINGW64) não é um sistema operacional suportado pelo script do `sail`.
> **Erro:** `Unsupported operating system [MINGW64_NT-10.0-26200]`.

### 3\. A Mudança para o Terminal Correto (Ubuntu WSL2)

Neste ponto, abandonamos o Git Bash e passamos a usar **exclusivamente** o terminal do **Ubuntu (WSL2)**.

```bash
# 1. Navega para a pasta do projeto (montada pelo Windows)
cd /mnt/c/Dev/aquashift

# 2. Cria o atalho 'sail' (desta vez, para o Linux)
echo "alias sail='./vendor/bin/sail'" >> ~/.bashrc
source ~/.bashrc

# 3. Tenta subir o Sail (Falhou)
sail up -d
```

> **Por quê?** O Docker falhou ao tentar criar os volumes.
> **Erro:** `Error response from daemon: ... OCI runtime create failed: ... no such file or directory`.

```bash
# 4. [CORREÇÃO] Limpeza total do Docker
sail down -v # Desliga os containers e apaga os volumes do projeto
docker system prune -a --volumes # Limpa todo o cache do Docker (imagens, redes, etc)
rm compose.yaml # Remove um arquivo duplicado que apareceu
```

> **Correção:** Tivemos que reiniciar o Docker Desktop manualmente e recriar o `docker-compose.yml` (que estava com erro de sintaxe YAML na linha 15).

```bash
# 5. Tenta subir o Sail (Falhou)
sail up -d
```

> **Por quê?** O Docker/WSL não conseguiu se autenticar.
> **Erro:** `error getting credentials - err: exit status 1`.

> **Correção:** Fomos ao Docker Desktop, `Settings > Resources > WSL Integration`, e (re)ativamos a integração com o "Ubuntu". Também rodamos `wsl --shutdown` no PowerShell.

```bash
# 6. Tenta subir o Sail (SUCESSO!)
sail up -d
```

> **Vitória\!** O ambiente Docker (PHP + PGSQL) finalmente estava no ar.

-----

## Fase 3: Desenvolvimento do App (Ubuntu WSL2)

Com o ambiente rodando, começamos a construir o **AquaShift**.

```bash
# 1. Gera a chave do app (confirma que o PHP funciona)
sail artisan key:generate

# 2. Instala o Breeze (Falhou)
sail composer require laravel/breeze --dev
```

> **Por quê?** A lentidão do I/O Docker/Windows causou um timeout.
> **Erro:** `exceeded the timeout of 300 seconds`.

```bash
# 3. [CORREÇÃO] Aumenta o timeout do Composer
sail composer config --global process-timeout 0 # (0 = ilimitado)

# 4. Finaliza a instalação pendente
sail composer update

# 5. Instala o Spatie (usando o composer update)
sail composer require spatie/laravel-permission
sail composer update # (Necessário de novo para finalizar)

# 6. Roda o instalador do Breeze (agora de verdade)
sail artisan breeze:install # (Respondemos: vue, inertia, dark mode, pest)

# 7. Instala dependências NPM
sail npm install

# 8. Roda o servidor de desenvolvimento Vite
# (Este comando deve ficar rodando em seu próprio terminal)
sail npm run dev

# 9. Roda as migrações (tabelas do Breeze)
sail artisan migrate

# 10. Cria nossos Models e Migrations do CRUD
sail artisan make:model Service -mf
sail artisan make:model Branch -mf
sail artisan make:model Bay -mf
sail artisan make:model Booking -mf

# 11. Roda as migrações (nossas tabelas)
sail artisan migrate

# 12. Publica os arquivos do Spatie (migração + config)
sail artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# 13. Roda a migração (tabelas do Spatie)
sail artisan migrate

# 14. Cria o Seeder de papéis
sail artisan make:seeder RolesAndPermissionsSeeder

# 15. Reseta o banco e roda todos os seeders
sail artisan migrate:fresh --seed
```

-----

## Fase 4: Desenvolvimento do CRUD (Ubuntu WSL2)

Comandos usados para construir, depurar e otimizar nosso primeiro CRUD (Filiais).

```bash
# 1. Cria o controlador do CRUD (com todos os métodos)
sail artisan make:controller Admin/BranchController --resource
```

> **Por quê?** Cria o arquivo `app/Http/Controllers/Admin/BranchController.php`. A flag `--resource` já cria todos os 7 métodos (index, create, store, edit, update, destroy).

```bash
# 2. Entra no console interativo
sail artisan tinker
```

> **Por quê?** Permite executar código PHP diretamente no terminal. Usamos isso para criar nosso usuário Admin manualmente.
>
> **Comandos dentro do Tinker:**
>
> ```php
> # Cria o usuário Admin
> $user = \App\Models\User::create([
>     'name' => 'Angel Luz',
>     'email' => 'seu-email@exemplo.com',
>     'password' => bcrypt('sua_senha_segura')
> ])
> ```
> 
> ```php
> # Atribui o papel 'Admin' (que o Seeder criou)
> $user-\>assignRole('Admin')
> ```
>
> ```php
> # Sai do Tinker
> exit
> ```

```bash
# 3. [CORREÇÃO DE ERRO] Limpa o cache de rotas
sail artisan route:clear
```

> **Por quê?** Usamos isso quando o Breeze não limpou a rota `/` (welcome) e tivemos o erro `View [welcome] not found`.

```bash
# 4. [CORREÇÃO DE ERRO] Reinicia o Vite
# (Ctrl + C para parar)
sail npm run dev
```

> **Por quê?** Usamos isso quando o Inertia/Vite não encontrava nossos novos arquivos `.vue` (como `Admin/Branches/Index.vue`).
> **Erro:** `Page not found: ./Pages/Admin/Branches/Index.vue`.

```bash
# 5. [OTIMIZAÇÃO] Limpa todos os caches (config, rotas, etc)
sail artisan optimize:clear
```

> **Por quê?** Usamos isso quando mudamos o `.env` ou `routes/web.php` e o Laravel não "via" as mudanças.

```bash
# 6. [OTIMIZAÇÃO] Cria o cache de configuração
sail artisan config:cache

# 7. [OTIMIZAÇÃO] Cria o cache de rotas
sail artisan route:cache
```

> **Por quê?** Esta dupla foi a **correção para a lentidão** (`F5` demorado). Forçamos o Laravel a ler 2 arquivos de cache em vez de 50+ arquivos de configuração/rotas, resolvendo o gargalo de I/O do Docker/Windows.

-----

## Fase 5: Publicação no GitHub (Ubuntu WSL2)

Comandos finais para publicar nosso código no portfólio.

```bash
# 1. Inicializa o repositório Git local
git init

# 2. Renomeia a branch principal (boa prática)
git branch -M main

# 3. Verifica o .gitignore (para garantir que .env, vendor/ etc. estão ignorados)
ls -la .gitignore

# 4. Adiciona todos os arquivos ao "stage"
git add .

# 5. Faz o primeiro commit (semântico)
git commit -m "feat: Initial project setup with Sail, Breeze, RBAC, and documentation"

# 6. Adiciona o link do repositório remoto (do GitHub)
git remote add origin https_url_do_github_aqui/angelluzk/aquashift.git

# 7. Envia (push) os arquivos para o GitHub
git push -u origin main
```