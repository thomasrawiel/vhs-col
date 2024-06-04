# vhscol
A collection of more or less useful ViewHelpers

# Usage
Namespace is registered globally as `vcol`

### URI Phone number
example with Address phone number
```
<f:link.typolink parameter="{linv:uri.telephone(phoneNumber: address.phone)}">{address.phone}</f:link.typolink>
```

## SVG
### Render SVG Content
render a svg filereference as inline svg html
```
 <f:format.raw><lin:renderSvgContent svgReference="{file}"/>/f:format.raw>
```

### Svg VH
render SVG from given name, has typoscript config

## Text
### Pipe to BR
If you don't allow <br> e.g. in the header, you can use `|` symbols. This vh replaces the | with <br> tag

## Misc
### Exension loaded
Condition if any extension key is loaded
```
<linv:extension.extensionLoaded extensionKey="my_ext">
<f:then></f:then>
<f:else></f:else>
</linv:extension.extensionLoaded>

<linv:extension.extensionLoaded extensionKey="my_ext">
    do something
</linv:extension.extensionLoaded>
```