# TYPO3 Focus Point Demo

**TYPO3's native `focusArea` + CSS `object-position` = focus-aware image cropping without JavaScript.**

**[Live demo](https://wazum.github.io/typo3-focus-point/)**

## What's inside

| Directory | What | How to run |
|-----------|------|------------|
| Root | TYPO3 v14 LTS + ddev + sitepackage | `ddev start` then [setup](#typo3-setup) |
| `docs-demo/` | Interactive static demo (Vite + TS) | `cd docs-demo && npm i && npm run dev` |

## The thesis

```css
.focus-image {
    object-fit: cover;
    object-position: var(--focus-position, 50% 50%);
}
```

The ViewHelper computes `--focus-position` from TYPO3's native `focusArea` crop data. No JavaScript needed.

### The math

TYPO3 stores [`focusArea`](https://docs.typo3.org/m/typo3/reference-tca/main/en-us/ColumnsConfig/Type/ImageManipulation/Index.html#add-a-focus-area) as `{x, y, width, height}` — floats 0–1 relative to the original image. The crop area defines what the server physically crops.

```
focusCenterX = focusArea.x + focusArea.width / 2
focusCenterY = focusArea.y + focusArea.height / 2

posX = (focusCenterX - cropArea.x) / cropArea.width * 100
posY = (focusCenterY - cropArea.y) / cropArea.height * 100

→ --focus-position: posX% posY%
```

## TYPO3 setup

```bash
ddev start
ddev composer create-project "typo3/cms-base-distribution:^14"
ddev composer req vendor/focuspoint-sitepackage:@dev
ddev typo3 setup \
  --server-type=other --driver=mysqli --host=db --port=3306 \
  --dbname=db --username=db --password=db \
  --admin-username=admin --admin-user-password='Focuspoint1!' \
  --admin-email=admin@example.com \
  --project-name='Focus Point Demo' \
  --create-site="https://typo3-focus-point.ddev.site" --force
ddev typo3 cache:flush
```

Then update `config/sites/main/config.yaml` to depend on the sitepackage:

```yaml
dependencies:
  - vendor/focuspoint-sitepackage
```

### Backend login

- URL: https://typo3-focus-point.ddev.site/typo3/
- User: `admin`
- Password: `Focuspoint1!`

### Creating demo content

1. Log into the TYPO3 backend
2. Create a root page "Home" (should already exist from `--create-site`)
3. Add a **Text & Media** content element
4. Upload an image via the **Assets** tab
5. Click the crop icon — you'll see "Desktop" and "Mobile" variant tabs
6. Drag the **focus area** (green dashed rectangle) to cover the important subject
7. Save — the frontend renders with `object-position` computed from the focus area

## The key file

`packages/focuspoint_sitepackage/Classes/ViewHelpers/FocusPositionViewHelper.php`

Reads `focusArea` from `sys_file_reference.crop`, computes the focus center, remaps it into cropped-image coordinate space, and returns a CSS `object-position` value used via CSS custom property `--focus-position`.

## Running tests

```bash
ddev exec './vendor/bin/phpunit --bootstrap vendor/typo3/testing-framework/Resources/Core/Build/UnitTestsBootstrap.php packages/focuspoint_sitepackage/Tests/Unit --testdox'
```

## License

GPL-2.0-or-later
