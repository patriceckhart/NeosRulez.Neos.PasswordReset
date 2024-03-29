# A Neos CMS package that adds password reset functionality to Neos backend login form.

NeosRulez.Neos.PasswordReset adds a password reset form to Neos backend login screen, so that users who have forgotten their password can easily reset it.
An electronic address of the email type must be stored with the user or the Neos backend username must correspond to an email address.

![PasswordReset](https://raw.githubusercontent.com/patriceckhart/NeosRulez.Neos.PasswordReset/master/Preview.gif)

## Installation

The NeosRulez.Neos.PasswordReset is listed on packagist (https://packagist.org/packages/neosrulez/neos-passwordreset) - therefore you don't have to include the package in your "repositories" entry any more.

Just run ```composer require neosrulez/neos-passwordreset```

## Settings.yaml

```yaml
NeosRulez:
  Neos:
    PasswordReset:
      senderMail: 'noreply@foo.com'
      adminMail: 'admin@foo.com' # Disable (set it to false) if you not want to recieve info mails.
      passwordLength: 8
      tokenLifetime: 5 # Reset token lifetime in minutes
      confirmation:
        templatePathAndFilename: 'resource://NeosRulez.Neos.PasswordReset/Private/Templates/Confirmation.html'
      reset:
        templatePathAndFilename: 'resource://NeosRulez.Neos.PasswordReset/Private/Templates/Reset.html'
      mailVariables:
        foo: 'bar' # Custom variables for your mails
```

## Author

* E-Mail: mail@patriceckhart.com
* URL: http://www.patriceckhart.com
