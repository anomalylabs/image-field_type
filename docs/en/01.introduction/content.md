## Introduction[](#introduction)

An image upload, select, and cropper field type.

<div class="alert alert-danger">**Heads Up:** The Image field type is a paid addon! Please checkout our store or ask about our developer partnership for repository access.</div>


### Configuration[](#introduction/configuration)

Below is the full configuration available with defaults values:

    "example" => [
        "type"   => "anomaly.field_type.image",
        "config" => [
            "folders"       => null,
            "aspect_ratio"  => null,
            "min_height"    => 400
        ]
    ]

###### Configuration

<table class="table table-bordered table-striped">

<thead>

<tr>

<th>Key</th>

<th>Example</th>

<th>Description</th>

</tr>

</thead>

<tbody>

<tr>

<td>

folders

</td>

<td>

`["images", "slides"]`

</td>

<td>

The slugs, paths, or IDs of allowed file folders. Defaults to all folders.

</td>

</tr>

<tr>

<td>

aspect_ratio

</td>

<td>

1:1

</td>

<td>

The locked aspect ration of the crop box.

</td>

</tr>

<tr>

<td>

min_height

</td>

<td>

500

</td>

<td>

The minimum height of the cropper area.

</td>

</tr>

</tbody>

</table>


### Installation[](#introduction/installation)

The Image field type is a paid addon and requires purchasing from the addon store OR a paid subscription.

##### Installing from Store Download

You can install the Templates module by downloading the addon and placing it within your site's addon directory:

    /addons/{application_ref}/anomaly/*

##### Installing with Composer Subscription

You can install the Image field type with Composer as a VCS repository if you have a subscription:

    {
         "require": {
            "anomaly/image-field_type": "~1.2.0"
        },
        "repositories": [
            {
                "type": "vcs",
                "url": "https://github.com/anomalylabs/image-field_type"
            }
        ]
    }
