# `mateffy/pan-analytics-viewer`

A tiny package to view your [panphp/pan](https://github.com/panphp/pan) analytics directly in the UI where they are triggered!

## Installation

```bash
composer require mateffy/pan-analytics-viewer
```

## Usage

To add the popups to your app, all you have to do is include the `pan-analytics::viewer` component in your blade template:

```blade
{{--    Make sure to verify who has access! 
        Including this component will expose your analytics data! --}}

@if (auth()->user()?->email === 'admin@example.com')
    <x-pan-analytics::viewer />
@endif
```

The popups should now be appearing when hovering over elements that have a `[data-pan]` attribute.

### Options

You can pass options to the component to change the default behavior:

```blade
<x-pan-analytics::viewer
    :toggle="true"
    :events="['my-event-1', 'my-event-2']"
    :force-all="true"
/>
```

| Option      | Description                                                                                  | Default |
|-------------|----------------------------------------------------------------------------------------------|---------|
| `toggle`    | Whether to show a toggle button to show/hide the popups                                      | `false` |
| `events`    | Specify the events that should be fetched.                                                   | `null`  |
| `force-all` | Force all events to get fetched, may be required when dynamically creating tracked elements. | `false` |

### Events

The package will automatically detect what events are being tracked on the current page by querying for `data-pan` attributes. If you are dynamically creating tracked elements after initial render, these may be missed and no popup will be shown.

To fix this, you can either specify the specific `events` you want to show on the page or use the `force-all` option to disable filtering and fetch all events.

## Security

The package registers a route for the client to be able to access the data. This route required a valid URL signature to be able to access it, which the `pan-analytics::viewer` component will automatically generate. **If you include this component on a page that is publicly accessible and don't check the user before including the component, anyone can access the data!**

So, make sure to only let users see this component if they have the necessary permissions.

```blade
@if (auth()->user()?->email === 'admin@example.com')
    <x-pan-analytics::viewer />
@endif

{{-- or --}}

@if (auth()->user()?->can('view-analytics'))
    <x-pan-analytics::viewer />
@endif
```
