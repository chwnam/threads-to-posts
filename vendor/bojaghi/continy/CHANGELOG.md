# CHANGELOG

## 1.2.4

2025-08-12

- Add test for missing arguments completion.
- Fix namespace error.

## 1.2.3

2025-07-15

- Add try-catch block inside `Continy::parseCallback`.

## 1.2.2

2025-07-14

- Fix `Continy::parseCallback` bug.

## 1.2.1

2025-07-14

- Fix missing implements.

## 1.2.0

2025-07-14

- Update `bojaghi/contract` to 1.3.
- Remove `Continy::concatName` method.

## 1.1.5

2025-07-14

- Update `bojaghi/contract` to 1.2. No breaking changes.
- Replace `custom.dic` to `bojaghi/dictionary`'s version.

## 1.1.4

2025-05-05

- Fix error when underscored(_) modules were not loaded correctly if class names are used directly.

## 1.1.3

2025-04-19

- Fix errors when injecting union type with default parameter.

## 1.1.2

2025-04-19

- Fix errors when injecting union types.
- Fix errors when parameter has default value.

## 1.1.1

2025-04-12

- Fix error when continy tries to instantiate by FQCN, but the class is aliased in the configuration.
- Update vendor libraries.

## 1.1.0

2024-12-25

- Support incomplete argument setup.

## 1.0.3

2024-12-21

- Export `Container` interface to `Bojaghi/Contract` package.

## 1.0.2

2024-12-11

- Support underscore(_) built-in modules.

## 1.0.1

2024-12-09

- Support casting of non-array arguments to array arguments.

## 1.0.0

Initial version.
