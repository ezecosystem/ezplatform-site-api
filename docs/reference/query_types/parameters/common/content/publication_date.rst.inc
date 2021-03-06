``publication_date``
~~~~~~~~~~~~~~~~~~~~

Defines the publication date of the Content as a timestamp.

- **value type**: ``integer``
- **value format**: ``single``, ``array``
- **operators**: ``eq``, ``in``, ``gt``, ``gte``, ``lt``, ``lte``, ``between``
- **target**: none
- **required**: ``false``
- **default**: not defined

Examples:

.. code-block:: yaml

    # identical to the example below
    publication_date: 1535117737

.. code-block:: yaml

    depth:
        eq: 1535117737

.. code-block:: yaml

    # identical to the example below
    publication_date: [1435117737, 1535117737]

.. code-block:: yaml

    publication_date:
        in: [1435117737, 1535117737]

.. code-block:: yaml

    # multiple operators are combined with logical AND
    publication_date:
        gt: '29 June 1991'
        lte: '5 August 1995'

.. code-block:: yaml

    publication_date:
        gt: 'today'

.. code-block:: yaml

    publication_date:
        between: ['today', '+1 week 2 days 4 hours 2 seconds']
