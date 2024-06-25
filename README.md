# Easy Contacts

Para trabalhar com o projeto, talvez seja necessária a instalação das seguintes coisas.

- PHP 8.3
- Apache2
- php-cli php-xml php-mbstring php-curl libapache2-mod-php php-mysql zip unzip php-zip
- and [Composer](https://getcomposer.org/download/)

Para iniciar o projeto e ver como está rodando, digite o seguinte comando:

```bash
docker build -t easycontacts .
```

Para rodar localmente, é necessário executar a imagem Docker com o comando.

```bash
docker run -d -p 80:80 -p 443:443 easycontacts 
```

## Setup CA

Para configurar o CA na máquina de desenvolvimento, é necessário baixar o ca.crt que está na URL.
ca.easycontacts.com/ca.crt e instalar o certificado no navegador.

## Setup DNS

Após isso, precisamos configurar o DNS, para isso, instalamos o resolvconf com o comando.

```bash
sudo apt install resolvconf 
```

Depois disso, precisamos ir até o arquivo.
`/etc/resolvconf/resolv.conf.d/head`<br>
Editamos o arquivo para ter o seguinte endereço (editar com sudo).

````bash
nameserver 10.161.222.16 # Ip da máquina dns
````

## Para criar um novo usuário no GitLab.

Criar o usuário na máquina do GitLab com o comando.

```bash
sudo adduser moraski
```

Depois disso, na interface gráfica, ir até Admin Area -> Users -> New -> e preencha os campos.<br>
Voltando ao terminal da máquina GitLab.<br>

```bash
sudo su
su - moraski
mutt -f /var/mail/moraski
# Pegar o link do email e alterar a senha
```

## Configurar o git com SSH.

Gere uma chave SSH em seu computador e coloque no GitLab para poder fazer commits diretos no servidor GitLab.

## Pipeline.

Temos 3 stages aqui.

- Build
- Test
- deploy

Começamos com a definição deles e seguido de um driver do Docker para deixar a execução dá pipeline mais rápida.

Usaremos o Docker:dind para rodar as imagens Docker, e antes de qualquer script, executamos o login no dockerhub

### Passo build-app.

```bash
build-app:
  stage: build
  script:
    - composer update
    - composer install
    - composer dump-autoload
    - echo "Composer update/install/dump-autoload Ok!✅"
    - docker build -t joaomoraski/easycontacts:latest .
    - echo "Docker Image Build Ok!✅"
    - docker push joaomoraski/easycontacts:latest
    - echo "Docker Push Ok!✅"
```

Aqui instalamos as dependências gerenciadas pelo composer e fazemos o setup do autoload também.

### Stage test

```bash
unit-tests-logic:
  stage: test
  script:
    - docker run --rm joaomoraski/easycontacts:latest ./vendor/bin/phpunit tests/contactTests --testdox
    - echo "All tests passed!✅"

unit-tests-utils:
  stage: test
  script:
    - docker run --rm joaomoraski/easycontacts:latest ./vendor/bin/phpunit tests/utilsTests --testdox
    - echo "All tests passed!✅"
```

Nesse caso, executamos os testes lógicos e testes unitários. Teste lógico, neste caso foi uma coisa criada para este.
Projeto apenas.

### Stage deploy

```bash
deploy-prod:
  stage: deploy
  rules:
    - if: $CI_COMMIT_BRANCH == $CI_DEFAULT_BRANCH
      when: on_success
    - when: never
  script:
    - chmod 400 $FILE_UBUNTU_RUNNER
    - ssh -i $FILE_UBUNTU_RUNNER -o "StrictHostKeyChecking No" ubuntu@web.easycontacts.com "docker login -u joaomoraski -p $DOCKER_HUB_ACCESS_TOKEN"
    - ssh -i $FILE_UBUNTU_RUNNER -o "StrictHostKeyChecking No" ubuntu@web.easycontacts.com "docker pull joaomoraski/easycontacts:latest"
    - echo "Docker pull on server Ok!"
    - ssh -i $FILE_UBUNTU_RUNNER -o "StrictHostKeyChecking No" ubuntu@web.easycontacts.com "docker stop easycontacts || true && docker rm easycontacts || true"
    - ssh -i $FILE_UBUNTU_RUNNER -o "StrictHostKeyChecking No" ubuntu@web.easycontacts.com "docker run -d --name easycontacts -p 80:80 -p 443:443 joaomoraski/easycontacts:latest"
    - echo "Docker deploy on server Ok!"
  environment: production
```

Aqui verificamos se estamos na branch master, se sim, durante a Pipeline ele vai se conectar com o docker hub, puxar a
imagem e executar baseado no necessário, a lógica para fazer o deploy, onde pode ser: parar a imagem antiga e subir a
nova ou, se for a primeira vez, apenas rodar a imagem nova.