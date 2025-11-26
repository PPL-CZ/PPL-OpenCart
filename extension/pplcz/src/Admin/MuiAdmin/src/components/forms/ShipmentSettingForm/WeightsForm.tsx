import { Fragment, useEffect, useMemo, useState } from "react";
import { components } from "../../../schema";
import { useForm, Controller, useFieldArray, useFormContext } from "react-hook-form";
import {
  Paper,
  Switch,
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableRow,
  TextField,
  Button,
  Typography,
  TableContainer,
} from "@mui/material";

export const WeightsForm = (props: {
  data: components["schemas"]["ShipmentMethodSettingModel"];
  costByWeight?: boolean;
}) => {
  const { getValues, control, setValue, watch } = useFormContext<components["schemas"]["ShipmentMethodSettingModel"]>();

  const [currencies, setCurrencies] = useState(() => {
    return props.data.currencies.filter(x => x.enabled).map(x => x.currency);
  });

  const costByWeight = watch("costByWeight");
  const parcelBoxes = watch("parcelBoxes");

  const addCurrency = (currency: string) => {
    let newCurrencies = currencies;

    const value = getValues();

    if (!value.currencies.some(x => x.currency === currency)) {
      value.currencies = value.currencies.concat([
        {
          currency,
          enabled: false,
        },
      ]);
    }

    value.weights = value.weights.map(x => {
      if (x.prices.some(y => y.currency === currency)) return x;
      x.prices = x.prices.concat([
        {
          currency,
        },
      ]);

      return {
        ...x,
      };
    });

    setValue("currencies", value.currencies);
    setValue("weights", value.weights);
    setCurrencies([...newCurrencies, currency]);
  };

  const allCurrencies = props.data.currencies.map(x => x.currency);

  const freeCurrencies = useMemo(
    () => allCurrencies.filter(x => currencies.indexOf(x) === -1),
    [allCurrencies, currencies]
  );

  const [selected, setSelected] = useState(props.data.currencies.filter(x => x.enabled)?.[0]?.currency || "");

  const inputName = (basename: string, currency: string) => {
    return `woocommerce_${basename}_${currency}`;
  };
  const inputWeightName = (...args: (string | number)[]) => `woocommerce_weights${args.map(x => `[${x}]`).join("")}`;

  const values = getValues();

  const { fields, append, remove } = useFieldArray({
    control,
    name: "weights",
  });

  const isLineThrough = (is: boolean) => {
    if (is)
      return {
        textDecoration: "line-through",
      };
    return {};
  };

  const isHidden = (is: boolean) => {
    if (is)
      return {
        display: "none",
      };
    return {};
  };

  return (
    <>
      <TableContainer component={Paper}>
        <Table className={"wc_input_table widefat"}>
          <TableHead>
            <TableRow>
              <TableCell colSpan={currencies.length + 1}>
                {currencies.map((x, index) => {
                  return (
                    <Fragment>
                      {index === 0 ? null : <>&nbsp;|&nbsp;</>}
                      {x === selected ? (
                        <>{x}</>
                      ) : (
                        <a
                          href={`#${x}`}
                          style={{
                            fontWeight: selected === x ? "bold" : "normal",
                          }}
                          onClick={e => {
                            e.preventDefault();
                            setSelected(x);
                          }}
                        >
                          {x}
                        </a>
                      )}
                    </Fragment>
                  );
                })}
                &nbsp;
                {freeCurrencies.length > 0 ? (
                  <select
                    key={freeCurrencies.join("")}
                    onChange={e => {
                      if (e.target.value) {
                        addCurrency(e.target.value);
                        setSelected(e.target.value);
                      }
                    }}
                  >
                    <option value={""} selected={true}>
                      Přidat měnu
                    </option>
                    {(freeCurrencies ?? []).map(x => (
                      <option value={x}>{x}</option>
                    ))}
                  </select>
                ) : null}
              </TableCell>
            </TableRow>
          </TableHead>
          {currencies.map(currency => {
            const style = (() => (currency === selected ? {} : { display: "none" }))();
            const selectedCurrenciesIndex = values.currencies.findIndex(x => x.currency === currency);
            return (
              <TableBody style={style}>
                <TableRow>
                  <TableCell
                    style={{
                      whiteSpace: "nowrap",
                      width: "1px",
                    }}
                  >
                    Povolení měny
                  </TableCell>
                  <TableCell className={"compound"}>
                    <Controller
                      key={selectedCurrenciesIndex}
                      control={control}
                      render={({ field, fieldState, formState }) => {
                        const val = field.value;
                        return (
                          <Switch
                            name={inputName("cost_allow", currency)}
                            onChange={() => {
                              field.onChange(!val);
                            }}
                            value="1"
                            checked={!!val}
                          />
                        );
                      }}
                      name={`currencies.${selectedCurrenciesIndex}.enabled`}
                    />
                  </TableCell>
                </TableRow>
                <TableRow>
                  <TableCell
                    style={{
                      whiteSpace: "nowrap",
                      width: "1px",
                    }}
                  >
                    Od jaké ceny bude doprava zadarmo
                  </TableCell>
                  <TableCell>
                    <Controller
                      key={selectedCurrenciesIndex}
                      control={control}
                      render={({ field, fieldState, formState }) => {
                        const val = field.value;
                        return (
                          <TextField
                            type="text"
                            name={inputName("cost_order_free", currency)}
                            onChange={x => {
                              field.onChange(x);
                            }}
                            value={val ?? ""}
                          />
                        );
                      }}
                      name={`currencies.${selectedCurrenciesIndex}.costOrderFree`}
                    />
                  </TableCell>
                </TableRow>
                {["CZK", "EUR", "PLN", "HUF", "RON"].indexOf(selected) > -1 ? (
                  <>
                    <TableRow>
                      <TableCell
                        style={{
                          whiteSpace: "nowrap",
                          width: "1px",
                        }}
                      >
                        Příplatek za dobírku
                      </TableCell>
                      <TableCell>
                        <Controller
                          key={selectedCurrenciesIndex}
                          control={control}
                          render={({ field, fieldState, formState }) => {
                            const val = field.value;
                            return (
                              <TextField
                                type="text"
                                name={inputName("cost_cod_fee", currency)}
                                onChange={x => {
                                  field.onChange(x);
                                }}
                                value={val ?? ""}
                              />
                            );
                          }}
                          name={`currencies.${selectedCurrenciesIndex}.costCodFee`}
                        />
                      </TableCell>
                    </TableRow>
                    <TableRow>
                      <TableCell
                        style={{
                          whiteSpace: "nowrap",
                          width: "1px",
                        }}
                      >
                        Příplatek i v případě bezplatné dopravy u dobírky
                      </TableCell>
                      <TableCell className={"compound"}>
                        <Controller
                          key={selectedCurrenciesIndex}
                          control={control}
                          render={({ field, fieldState, formState }) => {
                            const val = field.value;
                            return (
                              <input
                                type="checkbox"
                                className="checkbox"
                                name={inputName("cost_cod_fee_always", currency)}
                                onChange={x => {
                                  field.onChange(!val);
                                }}
                                value="1"
                                checked={!!val}
                              />
                            );
                          }}
                          name={`currencies.${selectedCurrenciesIndex}.costCodFeeAlways`}
                        />
                      </TableCell>
                    </TableRow>
                    <TableRow>
                      <TableCell
                        style={{
                          whiteSpace: "nowrap",
                          width: "1px",
                        }}
                      >
                        Od jaké ceny bude doprava zadarmo pro dobírku
                      </TableCell>
                      <TableCell>
                        <Controller
                          key={selectedCurrenciesIndex}
                          control={control}
                          render={({ field, fieldState, formState }) => {
                            const val = field.value;
                            return (
                              <TextField
                                type="text"
                                name={inputName("cost_order_free_cod", currency)}
                                onChange={x => {
                                  field.onChange(x);
                                }}
                                value={val ?? ""}
                              />
                            );
                          }}
                          name={`currencies.${selectedCurrenciesIndex}.costOrderFreeCod`}
                        />
                      </TableCell>
                    </TableRow>
                  </>
                ) : null}
                <TableRow style={isHidden(!!costByWeight)}>
                  <TableCell>Cena za dopravu</TableCell>
                  <TableCell>
                    <Controller
                      key={selectedCurrenciesIndex}
                      control={control}
                      render={({ field, fieldState, formState }) => {
                        const val = field.value;
                        return (
                          <TextField
                            type="number"
                            name={inputName("cost", currency)}
                            onChange={x => {
                              field.onChange(x);
                            }}
                            value={val ?? ""}
                          />
                        );
                      }}
                      name={`currencies.${selectedCurrenciesIndex}.cost`}
                    />
                  </TableCell>
                </TableRow>
              </TableBody>
            );
          })}
        </Table>
      </TableContainer>
      <Typography sx={{ ml: 2, mt: 2, mb: 2 }} component="h3" style={isHidden(!costByWeight)}>
        Ceny za dopravu a váhu
      </Typography>
      <Typography
        component="p"
        sx={{ ml: 2 }}
        style={{ ...isHidden(!costByWeight), ...{ marginTop: "1em", marginBottom: "1em" } }}
      >
        Tato funkce umožňuje automatické stanovení ceny dopravy na základě celkové hmotnosti objednávky. Po překročení
        definovaných hmotnostních hranic se cena dopravy upravuje dle nastavených pravidel. Nevyplněný parametr Od se
        bere jako 0 (do jako maximum), proto je nutné vždy stanovit koncovou hranici váhy pro správný výpočet ceny. V
        případě, že váha zásilky odpovídá více pravidlům, vybírá se nejdražší doprava. Váha do je brána jako ostrá
        nerovnost, např. bude nastaveno na 5kg, pak to bude {"<"}5kg
      </Typography>
      <TableContainer style={isHidden(!costByWeight)} component={Paper}>
        <Table className={"wc_input_table widefat"}>
          <TableHead>
            <TableRow>
              <TableCell rowSpan={2}>Váha&nbsp;od (kg)</TableCell>
              <TableCell rowSpan={2}>Váha&nbsp;do (kg)</TableCell>
              {parcelBoxes ? <TableCell rowSpan={2}>Blokovaná VM</TableCell> : null}
              {currencies.length ? <TableCell colSpan={currencies.length + 1}>Cena</TableCell> : null}
            </TableRow>
            <TableRow>
              {currencies
                .filter(x => x === selected)
                .map(x => (
                  <TableCell key={x}>{x}</TableCell>
                ))}
              {currencies
                .filter(x => x !== selected)
                .map(x => (
                  <TableCell key={x}>{x}</TableCell>
                ))}
              <TableCell style={{ width: "50px" }} />
            </TableRow>
          </TableHead>
          <TableBody>
            {fields.map((row, index) => {
              return (
                <TableRow key={index}>
                  <TableCell>
                    <Controller
                      control={control}
                      render={({ field, fieldState, formState }) => {
                        const val = field.value;
                        return (
                          <TextField
                            type="number"
                            name={inputWeightName(index, "from")}
                            onChange={x => {
                              field.onChange(x);
                            }}
                            value={val ?? ""}
                            sx={{ width: "6em" }}
                          />
                        );
                      }}
                      name={`weights.${index}.from`}
                    />
                  </TableCell>
                  <TableCell>
                    <Controller
                      control={control}
                      render={({ field, fieldState, formState }) => {
                        const val = field.value;
                        return (
                          <TextField
                            type="number"
                            name={inputWeightName(index, "to")}
                            onChange={x => {
                              field.onChange(x);
                            }}
                            sx={{ width: "6em" }}
                            value={val ?? ""}
                          />
                        );
                      }}
                      name={`weights.${index}.to`}
                    />
                  </TableCell>
                  {parcelBoxes ? (
                    <TableCell>
                      <Controller
                        control={control}
                        render={({ field, fieldState, formState }) => {
                          const val = field.value;
                          return (
                            <>
                              <input
                                type="checkbox"
                                className="checkbox"
                                name={inputWeightName(index, "disabledParcelBox")}
                                onChange={x => {
                                  field.onChange(!val);
                                }}
                                value="1"
                                checked={!!val}
                              />{" "}
                              <span style={isLineThrough(!!val)}>Parcelboxy</span>
                            </>
                          );
                        }}
                        name={`weights.${index}.disabledParcelBox`}
                      />
                      <br />

                      <Controller
                        control={control}
                        render={({ field, fieldState, formState }) => {
                          const val = field.value;
                          return (
                            <>
                              <input
                                type="checkbox"
                                className="checkbox"
                                name={inputWeightName(index, "disabledAlzaBox")}
                                onChange={x => {
                                  field.onChange(!val);
                                }}
                                value="1"
                                checked={!!val}
                              />{" "}
                              <span style={isLineThrough(!!val)}>Alzaboxy</span>
                            </>
                          );
                        }}
                        name={`weights.${index}.disabledAlzaBox`}
                      />
                      <br />

                      <Controller
                        control={control}
                        render={({ field, fieldState, formState }) => {
                          const val = field.value;
                          return (
                            <>
                              <input
                                type="checkbox"
                                className="checkbox"
                                name={inputWeightName(index, "disabledParcelShop")}
                                onChange={x => {
                                  field.onChange(!val);
                                }}
                                value="1"
                                checked={!!val}
                              />{" "}
                              <span style={isLineThrough(!!val)}>ParcelShopy</span>
                            </>
                          );
                        }}
                        name={`weights.${index}.disabledParcelShop`}
                      />
                    </TableCell>
                  ) : null}
                  {currencies
                    .filter(x => x === selected)
                    .concat(currencies.filter(x => x !== selected))
                    .map(x => {
                      const index2 = row.prices.findIndex(y => y.currency === x);
                      return (
                        <TableCell key={x}>
                          <Controller
                            control={control}
                            render={({ field, fieldState, formState }) => {
                              const val = field.value;
                              return (
                                <TextField
                                  type="number"
                                  sx={{ width: "6em" }}
                                  name={inputWeightName(index, "prices", index2, "price")}
                                  onChange={x => {
                                    field.onChange(x);
                                  }}
                                  value={val ?? ""}
                                />
                              );
                            }}
                            name={`weights.${index}.prices.${index2}.price`}
                          />
                          <Controller
                            control={control}
                            render={({ field, fieldState, formState }) => {
                              return (
                                <input
                                  type="hidden"
                                  name={inputWeightName(index, "prices", index2, "currency")}
                                  value={field.value}
                                />
                              );
                            }}
                            name={`weights.${index}.prices.${index2}.currency`}
                          />
                        </TableCell>
                      );
                    })}
                  <TableCell
                    style={{
                      padding: "3px",
                    }}
                  >
                    <Button
                      className={"button"}
                      onClick={e => {
                        e.preventDefault();
                        remove(index);
                      }}
                    >
                      Smazat řádek
                    </Button>
                  </TableCell>
                </TableRow>
              );
            })}
            <TableRow>
              <TableCell
                colSpan={currencies.length + 3}
                style={{
                  padding: "3px",
                }}
              >
                <Button
                  className={"button"}
                  onClick={e => {
                    e.preventDefault();
                    append({
                      prices: currencies.map(x => ({
                        currency: x,
                      })),
                    });
                  }}
                >
                  Nový řádek
                </Button>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </TableContainer>
    </>
  );
};
