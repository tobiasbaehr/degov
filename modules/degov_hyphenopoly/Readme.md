# Hyphenopoly

This module integrates [mnater/Hyphenopoly](https://github.com/mnater/Hyphenopoly/) into Drupal to polyfill [CSS based hyphenation](https://developer.mozilla.org/en-US/docs/Web/CSS/hyphens) in browsers [not supporting CSS hyphens](https://caniuse.com/#search=hyphens).

You can configure which css selectors should be hyphenated to avoid unnecessary front end processing.

The module ensures also given CSS classes have hypenation enabled by adding a config based style sheet.

### Requirements

You need tio install [mnater/Hyphenopoly](https://github.com/mnater/Hyphenopoly/) as a library at *docroot/libraries/hyphenopoly/*. 

Make sure that all your your enabled site languages (langCode : \Drupal::languageManager()->getCurrentLanguage()->getId()) hase a corresponsing language folder at *docroot/libraries/hyphenopoly/lang/<your language>

### Add library using composer

In your root composer.json file add the following to the repositories section

```
  "repositories": {
  
    [... other repositories ... ], 
    
    "hyphenopoly": {
      "type": "package",
      "package": {
        "name": "mnater/hyphenopoly",
        "version": "v4.7.0",
        "type": "drupal-library",
        "dist": {
          "type": "zip",
          "url": "https://github.com/mnater/hyphenopoly/archive/v4.7.0.zip"
        }
      }
    },

```

### Settings

At `/admin/config/degov/hyphenopoly` you can add CSS selectors. Hyphonopoly hypheningwill be applied to all these selectors and their child elmenents.

### CSS

As hyphenopoly is a polyfill it will only apply to elements which have the CSS defined for it. This module will provide default styles, like: 

```
.your-first-selector,
.your-other-selector {
  hyphens: auto;
  -ms-hyphens: auto;
  -moz-hyphens: auto;
  -webkit-hyphens: auto;
}
```

### Notes

If you start using fancy words like *Aufmerksamkeitsdefizitmedikamentenbeipackzettelschriftfarbe mit Donaudampfschifffahrtsgesellschaft* you might not that there are still akward line breaks. **Words break only ONCE**. Consider replacing your Theodor-Fontane-style editor with more Homer Simpson type ;)

