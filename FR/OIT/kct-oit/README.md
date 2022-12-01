<h1>
<img src="https://s3.eu-west-2.amazonaws.com/kct-dev/assets/main-logo-mid.png" width="200"> 
 HumannConnect
</h1>

## Project Overview

> Humann Connect Project is here to get back the connections between human virtually by providing the real event feel

---

## Branch System

### Branch Naming Convention

Branches are divided into different versions

Developers are supposed to check out to respective branch by task version for example if task belongs to version 2.3
then respective branch should be checked out

> *NOTE: Version branch are categorised by `release/` prefix*

Each developer will be provided by a feature branch `leading with developer name in end` inside respective release version branch

> ***Example:** feature/version-2.3_gourav*

### Pull Request Create Guide

- Developers must always take pull before working on their branch
- After Completing the work commit your changes
- Now update the release version branch with latest code
  > Example: You have worked on `feature/version-2.3_someone` then `release/version-2.3` needs to `fetch|pull` after
  > commiting to your branch
- Merge the `release/version-X` to your feature branch
    - Resolve conflicts if any
- Push the merge
- Now Go to `BitBucket Pull Request` and generate the pull request from your `feature branch` to respective
  `version release` branch
  > Example: feature/version-2.3_gourav > release/version-2.3

---

## Project Setup Guide

### Project Links

> **Note:** To access the OIT part of application add the /oit in end of url for the following server
> **Example:** https://accountName.seque.in/oit

> - [Development Server (seque.in)](https://auth.seque.in)
> - [Staging Server (humannconnect.dev)](https://auth.humannconnect.dev)
> - [Production Server (humannconnect.com)](https://auth.humannconnect.com)

### Steps
1. Create the app password on bitbucket if not
    1. Login To BitBucket (Make sure you have permission to repo)
    2. From Top Right Corner Click on your badge icon
    3. Goto Setting > Personal Settings > App Passwords
    4. Click on create app password
    5. Give the proper permission to app so repo can be read|write
    6. Store the password of repo
2. Clone the BitBucket Repo
    1. Go to *[KCT OIT](https://bitbucket.org/kct-technologies/kct-oit/src/master/)*
    2. Click on **Clone**
    3. If you are the only working on that on local then add the
       >git clone https://***[USERNAME]***:***[APP PASSWORD]***@bitbucket.org/kct-technologies/kct-oit.git

       This will avoid asking for app password on each git operation
3. Go to project and run `npm install` to install the packages
4. Checkout to respective branch
5. Run again the `npm install` if you see `package.json` contains different package from master branch
    1. Update the `.env` according to server connection you want to make with local
        1. For `Development` server connection set the following details
           > REACT_APP_HO_TESTHOST=https://`ACCOUNT_NAME`.seque.in
        2. For `Staging` server connection set the following details
           > REACT_APP_HO_TESTHOST=https://`ACCOUNT_NAME`.humannconnect.dev
        3. For `Production` server connection set the following details
           > REACT_APP_HO_TESTHOST=https://`ACCOUNT_NAME`.humannconnect.com
6. Run the project by `npm start`

## Preparing Build

### Steps
1. Update the `.env`
   1. For `Development` server Build
      > REACT_APP_HO_HOSTNAME=seque.in
   2. For `Staging` server Build
      > REACT_APP_HO_HOSTNAME=humannconnect.dev
   3. For `Production` server Build
      > REACT_APP_HO_HOSTNAME=humannconnect.com

