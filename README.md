# Datafeedr Comparison Template

This plugin extends the datafeedr comparison sets template with a few set of options.

### Instal

1. Visit [https://github.com/NoorDigitalAgency/datafeedr-comparison-template/releases/](https://github.com/NoorDigitalAgency/datafeedr-comparison-template/releases/) and download zip
2. Upload zip file in wp plugin uploader
3. Install and activate

### Additional shortcode params

| Params              | Description                                | Values                    | Default      |
|---------------------|:------------------------------------------:|:-------------------------:|:------------:|
| display             | Display button, text link or product card  | (string) button/text/card | table              
| display_num         | How many products to show                  | (number)                  | -1
| display_at_position | Pick produkts by it's position in list     | (string) "1,2,3"          | null/undefined
| display_text        | This text goes as link or button text      | (string) any              | Product name
| display_class       | Prints css selector to element             | (string) any              | empty string
| display_styles      | Accepts a css string, key value seperated by ":" and each entry/property seperated with ",". example "color:red, background:blue" | (string)  | empty string

#### example usage to display only first product as button link
```
[dfrcs name="product name" display="button" display_text="Im a button" display_num="1"] 
```

### example usage to display a button with some extra styling options
```
[dfrcs name="product name" display="button" display_class="css-selector" display_styles="color:red, background:blue"]
```