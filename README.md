# Datafeedr Comparison Template

This plugin extends the datafeedr comparison sets template with a few set of options.

### Instal

1. Visit [https://github.com/NoorDigitalAgency/datafeedr-comparison-template/releases/](https://github.com/NoorDigitalAgency/datafeedr-comparison-template/releases/) and download zip
2. Upload zip file in wp plugin uploader
3. Install and activate

### Additional shortcode params

| Params            | Description                  | Values                   | Default      |
|-------------------|:----------------------------:|:------------------------:|:------------:|
| display           | button / text link           | (string) button | text   | Dfr table              
| display_num       | How many products to show    | (number)                 | -1
| display_text      | This text goes as link or    | (string) any             | Product name
|                   | button text                  |                          |

#### example usage to display only first product as button link
```
[dfrcs name="product name" display="button" display_text="Im a button" display_num="1"] 
```