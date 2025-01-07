# Light ORM

`Light ORM` создан с целью изучения создания собственного ORM. **Не готов к использованию в production среде!!!**

Реализует только запись в БД (`INSERT`, `UPDATE`) - Write-Only. Чтение (`SELECT`) необходимо осуществлять с помощью
нативных SQL запросов.

При создании вдохновлялся `Doctrine ORM` и `Cycle ORM`.

В проекте используются компоненты Doctrine.

## Прогресс

- [ ] Mapping - Class (Entity) Metadata
  - [ ] Fields / Columns
  - [ ] Primary Key
  - [ ] Associations
    - [ ] OneToOne
    - [ ] OneToMany
    - [ ] ManyToOne
    - [ ] manyToMany
  - [ ] Indexes (+ Unique)
  - [ ] Foreign Keys
  - [ ] Version (For Optimistic Lock)
- [ ] Object Hydration
- [ ] Persister
  - [ ] Saving (Insert, Update) Entity to DB
  - [ ] Loading Entity from DB
- [ ] Unit Of Work
    - [ ] Transactions
- [ ] Events
- [ ] IdGenerator