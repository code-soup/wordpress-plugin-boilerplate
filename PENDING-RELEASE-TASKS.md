# Pending release tasks

## Out of scope (future release)

- PHPUnit / Brain Monkey tests — **not** part of 0.0.4
- GitHub Actions CI — **not** part of 0.0.4

---

## Release preparation (manual, when you tag `0.0.4`)

- [ ] `npm run lint` and `npm run build:dev`
- [ ] Manual WordPress smoke test (admin + front with built `dist/`)
- [ ] Optional: dry-run `npm run setup` on a copy
- [ ] Git commit (if not already), tag `0.0.4`, GitHub release notes
- [ ] Confirm `composer.lock` changes are acceptable for the release artifact

---

*Architecture items from the review are implemented; see `CHANGELOG.md` `[Unreleased]` and `[0.0.4]`.*