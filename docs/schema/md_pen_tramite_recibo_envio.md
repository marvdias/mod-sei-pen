# md_pen_tramite_recibo_envio

## Description

<details>
<summary><strong>Table Definition</strong></summary>

```sql
CREATE TABLE `md_pen_tramite_recibo_envio` (
  `numero_registro` char(16) NOT NULL,
  `id_tramite` bigint(20) NOT NULL,
  `dth_recebimento` datetime NOT NULL,
  `hash_assinatura` varchar(1000) NOT NULL,
  PRIMARY KEY (`numero_registro`,`id_tramite`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci
```

</details>

## Columns

| Name | Type | Default | Nullable | Children | Parents | Comment |
| ---- | ---- | ------- | -------- | -------- | ------- | ------- |
| numero_registro | char(16) |  | false |  |  |  |
| id_tramite | bigint(20) |  | false |  |  |  |
| dth_recebimento | datetime |  | false |  |  |  |
| hash_assinatura | varchar(1000) |  | false |  |  |  |

## Constraints

| Name | Type | Definition |
| ---- | ---- | ---------- |
| PRIMARY | PRIMARY KEY | PRIMARY KEY (numero_registro, id_tramite) |

## Indexes

| Name | Definition |
| ---- | ---------- |
| PRIMARY | PRIMARY KEY (numero_registro, id_tramite) USING BTREE |

## Relations

![er](md_pen_tramite_recibo_envio.svg)

---

> Generated by [tbls](https://github.com/k1LoW/tbls)
