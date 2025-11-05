# BaseComponent Documentation

## Overview

`BaseComponent` is an abstract base class for building reusable form components in Laravel. It provides a robust foundation with common properties and functionality that most form elements require, eliminating code duplication and ensuring consistency across your form components.

## Key Features

- **Automatic Property Initialization**: Seamlessly binds Blade component attributes to PHP properties
- **Model Binding**: Supports auto-population of values from Eloquent models, collections, arrays, or objects
- **Flexible Label Management**: Show or hide labels with the `noLabel` option
- **CSS Class Management**: Accepts both string and array formats for CSS classes
- **Form States**: Built-in support for `required`, `disabled`, and `readonly` states
- **Smart Defaults**: Auto-generates IDs from names if not provided
- **Type Safety**: Validates required parameters and throws meaningful exceptions

## Properties

### Public Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `$name` | `string` | - | **Required**. The name attribute for the form element |
| `$id` | `string` | `$name` | The ID attribute (auto-generated from name if not provided) |
| `$type` | `string` | `''` | The type attribute (e.g., 'text', 'email', 'password') |
| `$label` | `string` | - | The label text (required unless `noLabel` is true) |
| `$noLabel` | `bool` | `false` | Whether to hide the label |
| `$value` | `mixed` | `''` | The current value of the form element |
| `$cssClasses` | `string` | `null` | Additional CSS classes (string or array) |
| `$required` | `bool` | `false` | Whether the field is required |
| `$disabled` | `bool` | `false` | Whether the field is disabled |
| `$readonly` | `bool` | `false` | Whether the field is readonly |
| `$placeholder` | `string` | `''` | Placeholder text for the form element |

### Protected Properties

| Property | Type | Description |
|----------|------|-------------|
| `$model` | `Model\|Collection\|null` | The model instance for data binding |
| `$field` | `string\|null` | The field name for model binding |

## Creating Custom Components

### Basic Example

To create a custom form component, extend `BaseComponent`:

```php
<?php

namespace App\View\Components\Forms;

use App\View\Components\Base\BaseComponent;
use Illuminate\Contracts\View\View;

class TextInput extends BaseComponent
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        $name = '',
        $id = '',
        $type = 'text',
        $label = '',
        $noLabel = false,
        $value = '',
        $cssClasses = [],
        $readonly = false,
        $required = false,
        $disabled = false,
        $placeholder = ''
    )
    {
        parent::__construct(
            $name,
            $id,
            $type,
            $label,
            $noLabel,
            $value,
            $cssClasses,
            $readonly,
            $required,
            $disabled,
            $placeholder
        );
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.forms.text-input');
    }
}
```

### Blade Template Example

Create the corresponding Blade view (`resources/views/components/forms/text-input.blade.php`):

```blade
<div class="form-group">
    @if(!$noLabel)
        <label for="{{ $id }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $id }}"
        value="{{ $value }}"
        class="form-control {{ $cssClasses }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $readonly ? 'readonly' : '' }}
    />
</div>
```

## Usage Examples

### Example 1: Basic Text Input

```blade
<x-forms.text-input
    name="username"
    label="Username"
    placeholder="Enter your username"
    required
/>
```

**Output**:
- Name: `username`
- ID: `username` (auto-generated)
- Label: "Username" with required asterisk
- Required validation enabled

### Example 2: Email Input with Custom ID

```blade
<x-forms.text-input
    name="email"
    id="user-email"
    type="email"
    label="Email Address"
    placeholder="user@example.com"
    required
/>
```

**Output**:
- Name: `email`
- ID: `user-email` (custom)
- Type: `email`
- Required validation enabled

### Example 3: Input Without Label

```blade
<x-forms.text-input
    name="search"
    :noLabel="true"
    placeholder="Search..."
/>
```

**Output**:
- No label rendered
- No validation error for missing label (because `noLabel` is true)

### Example 4: Input with CSS Classes (Array)

```blade
<x-forms.text-input
    name="phone"
    label="Phone Number"
    :cssClasses="['input-lg', 'border-primary', 'shadow-sm']"
/>
```

**Output**:
- CSS classes: `form-control input-lg border-primary shadow-sm`

### Example 5: Input with CSS Classes (String)

```blade
<x-forms.text-input
    name="phone"
    label="Phone Number"
    cssClasses="input-lg border-primary"
/>
```

**Output**:
- CSS classes: `form-control input-lg border-primary`

### Example 6: Disabled Input

```blade
<x-forms.text-input
    name="status"
    label="Status"
    value="Active"
    disabled
/>
```

**Output**:
- Field is disabled and cannot be edited

### Example 7: Readonly Input with Value

```blade
<x-forms.text-input
    name="created_at"
    label="Created Date"
    value="2024-01-15"
    readonly
/>
```

**Output**:
- Field displays the value but cannot be edited

## Model Binding

### Example 8: Binding with Eloquent Model

```php
// Controller
public function edit(User $user)
{
    return view('users.edit', compact('user'));
}
```

```php
// In your custom component constructor
public function __construct(
    $name = '',
    $model = null,
    // ... other parameters
)
{
    parent::__construct($name, /* ... */);
    
    if ($model) {
        $this->setModel($model);
    }
}
```

```blade
<!-- Blade view -->
<x-forms.text-input
    name="name"
    label="Full Name"
    :model="$user"
/>
```

**Output**:
- Automatically populates `value` with `$user->name`

### Example 9: Binding with Array

```php
// Controller
public function create()
{
    $defaults = [
        'country' => 'USA',
        'language' => 'en',
    ];
    
    return view('settings.create', compact('defaults'));
}
```

```blade
<!-- Blade view -->
<x-forms.text-input
    name="country"
    label="Country"
    :model="$defaults"
/>
```

**Output**:
- Automatically populates `value` with `USA`

### Example 10: Binding with Collection

```php
// Controller
public function edit()
{
    $settings = collect([
        'theme' => 'dark',
        'notifications' => true,
    ]);
    
    return view('settings.edit', compact('settings'));
}
```

```blade
<!-- Blade view -->
<x-forms.text-input
    name="theme"
    label="Theme"
    :model="$settings"
/>
```

**Output**:
- Automatically populates `value` with `dark`

## Advanced Component Example

### Creating a Select Component

```php
<?php

namespace App\View\Components\Forms;

use App\View\Components\Base\BaseComponent;
use Illuminate\Contracts\View\View;

class Select extends BaseComponent
{
    public array $options;
    public $selected;

    public function __construct(
        $name = '',
        $id = '',
        $label = '',
        $noLabel = false,
        $value = '',
        $options = [],
        $cssClasses = [],
        $required = false,
        $disabled = false,
        $model = null
    )
    {
        parent::__construct(
            $name,
            $id,
            '',
            $label,
            $noLabel,
            $value,
            $cssClasses,
            false,
            $required,
            $disabled,
            ''
        );

        $this->options = $options;
        
        if ($model) {
            $this->setModel($model);
        }
        
        $this->selected = $this->value;
    }

    public function render(): View
    {
        return view('components.forms.select');
    }
}
```

```blade
<!-- resources/views/components/forms/select.blade.php -->
<div class="form-group">
    @if(!$noLabel)
        <label for="{{ $id }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <select
        name="{{ $name }}"
        id="{{ $id }}"
        class="form-select {{ $cssClasses }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
    >
        @foreach($options as $key => $text)
            <option value="{{ $key }}" {{ $key == $selected ? 'selected' : '' }}>
                {{ $text }}
            </option>
        @endforeach
    </select>
</div>
```

**Usage**:

```blade
<x-forms.select
    name="role"
    label="User Role"
    :options="[
        'admin' => 'Administrator',
        'user' => 'Regular User',
        'guest' => 'Guest'
    ]"
    :model="$user"
    required
/>
```

## Validation and Error Handling

The `BaseComponent` class validates required parameters and throws exceptions when they're missing:

### Exception Examples

```php
// ❌ Missing required 'name' parameter
<x-forms.text-input label="Username" />
// Throws: InvalidArgumentException: El parámetro "name" es requerido

// ❌ Missing required 'label' when noLabel is false
<x-forms.text-input name="username" />
// Throws: InvalidArgumentException: El parámetro "label" es requerido cuando noLabel es false

// ❌ Invalid cssClasses type
<x-forms.text-input name="username" label="Username" :cssClasses="123" />
// Throws: InvalidArgumentException: El parámetro "cssClasses" debe ser de tipo array o string

// ❌ Invalid model type
$this->setModel("invalid"); // Inside component
// Throws: InvalidArgumentException: The "model" parameter must be of type Model, Collection, array, or object.
```

## Best Practices

1. **Always provide a name**: The `name` attribute is required and must not be empty.

2. **Provide labels for accessibility**: Unless you have a good reason, always provide a label. Use `noLabel` sparingly.

3. **Use model binding for edit forms**: It automatically populates values from your models, reducing boilerplate.

4. **Leverage CSS class arrays**: For complex styling, use arrays for better readability:
   ```blade
   :cssClasses="['input-lg', 'border-primary', 'bg-light']"
   ```

5. **Extend thoughtfully**: Add only properties specific to your component. Let `BaseComponent` handle common properties.

6. **Override when needed**: You can override `initializeBaseProperties()` in child components if you need custom initialization logic.

## Method Reference

### Public Methods

#### `getName(): string`
Returns the name attribute value.

#### `getId(): string`
Returns the ID attribute value.

#### `getType(): string`
Returns the type attribute value.

#### `getValue(): mixed`
Returns the current value of the form element.

#### `getLabel(): string`
Returns the label text.

### Protected Methods

#### `initializeBaseProperties(array $params): void`
Processes and validates component parameters. Called automatically by the constructor.

#### `getModel(?string $key = null): Model|Collection|mixed|null`
Retrieves the bound model or a specific attribute from it.

#### `setModel($model): Collection|null`
Binds a model to the component and auto-populates the value if the model contains a matching property.

## Troubleshooting

### Component not found
Make sure you've registered your component namespace in `config/app.php` or use anonymous components.

### Value not populating from model
Ensure the property name in your model matches the component's `name` attribute exactly.

### CSS classes not applying
Verify you're passing either a string or an array. Objects or other types will throw an exception.

### Label validation error
If you don't want a label, explicitly set `:noLabel="true"`.

## Contributing

When extending `BaseComponent`, consider:
- Maintaining backward compatibility
- Adding comprehensive PHPDoc comments
- Writing tests for new functionality
- Updating this README with new examples

---

**Version**: 1.0  
**Last Updated**: 2024-01-15  
**Maintainer**: Laravel Development Team
