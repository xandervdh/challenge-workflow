# A tomato challenge

## What?

making a ticket desk, [here is the exercise](readme/excersize.md)

## Who? 

a group project with:
- [Cis Magito](https://github.com/Beardificent)
- [Kayalin Van Kogelenberg](https://github.com/MonoraxXiV)
- [Xander Van der Herten](https://github.com/xandervdh)

## How?

We are using a [trello](https://trello.com/b/fJQVglRm/atomato-project) to organize everything.

Check it out if you want to see how far along we are.

## What did we do

- Looked at the database relations
![database](readme/db.png)
- made use of bin/console make: to generate most of the files.
- when working on new functions, we worked in a separate branch, once succesfull - instant merge.
- deployed to Heroku
    (https://atomato-br-master.herokuapp.com/public/)
- 

## Goals

- Working around with Symfony (command make:)
- 

## How to use this repository?

- clone repository to local machine.
- .env.local isn't included (is in our .gitignore), you will need to add this yourself and set the root:password:localhost:database name.

## Deploying Heroku for Symf 4&5

https://devcenter.heroku.com/articles/deploying-symfony4

- we did encounter a problem whilst deploying on Heroku (a composer runtime error), composer was running at 2.0 whilst for Heroku it needed to be running on composer 1.10.13. 
