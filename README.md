# ContactBundle
Bundle of Symfony for create contact forms

# Installation

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require vzenix/ContactBundle "~1"
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new VZenix\Bundle\ContactBundle\ContactBundle()
        );

        // ...
    }

    // ...
}
```

Step 3: Create structure in database
---------------------------

```console
php bin/console doctrine:schema:update --force
```

Step 4: set configuration in config.yml
---------------------------

```yml
# Contact page configuration
contact:
    
    # Configuration for mailing form (require swiftmailer=true)
    mail:
        subject: "Subject for mail"
        to: "<your email>"
        from: "your email"

    # templates to use in website and mailing
    templates:
        view: "default/contact.html.twig"
        mails: "emails/contact.html.twig"
        
    # Time in second betwen post request
    lapsus: "2"
    
    # Set true for log the form in database
    log: true
    
    # If you want a mail when form is send
    swiftmailer: true
```

Step 5: Personalize web template
---------------------------

```twig
    <!-- Main -->
    <div id="main" class="wrapper style1">

        <div class="container">
            <header class="major" id="messages_zone">
                <h2>Form contact</h2>
                <p>Lorem.</p>
            </header>

            <section class="error-form fade{% if messages_error != true %} hide{% endif %}">
                Ups, cannot send the form, review field and try again
            </section>

            <section class="ok-form fade{% if messages_sent != true %} hide{% endif %}">
                All is ok.
            </section>

            <!-- Form -->
            <section>

                <h3>Lorem.</h3>

                <form method="post" action="#">
                    <div class="row uniform 50%">
                        <div class="6u 12u$(xsmall)">
                            <input type="text" name="name" id="name" value="{{autocomplete.name}}" placeholder="Name" />
                        </div>
                        <div class="6u$ 12u$(xsmall)">
                            <input type="email" name="email" id="email" value="{{autocomplete.email}}" placeholder="Email" />
                        </div>
                        <div class="12u$">
                            <input type="text" name="calculator" id="calculator" value="" placeholder="Type the result of {{robot}} = ?" />
                        </div>
                        <div class="12u$">
                            <textarea name="message" id="message" placeholder="Your message" rows="6">{{autocomplete.message}}</textarea>
                        </div>
                        <div class="12u$">
                            <ul class="actions">
                                <li><input type="submit" value="Send message" class="special" /></li>
                                <li><input type="reset" value="Reset" /></li>
                            </ul>
                        </div>
                    </div>
                </form>
            </section>

        </div>
    </div>
```


Step 6: Personalize mailing template
---------------------------

```twig
<p>You have a message from your web</p>
<p>Name: {{name}}</p>
<p>Email: {{email}}</p>
<p>Message: {{message}}</p>
```
