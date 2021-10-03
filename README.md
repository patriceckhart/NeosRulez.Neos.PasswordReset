# A Neos CMS package that adds password reset functionality to Neos backend login form.

NeosRulez.Neos.PasswordReset adds a password reset form to Neos backend login screen, so that users who have forgotten their password can easily reset it.

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
      adminMail: 'admin@foo.com' # Disable if you not want to recieve info mails.
      templatePathAndFilename: 'resource://NeosRulez.Neos.PasswordReset/Private/Templates/Mail.html'
```

## Double Opt-in ...

... is coming soon.

## Author

* E-Mail: mail@patriceckhart.com
* URL: http://www.patriceckhart.com
